<?php

    require_once __DIR__ . '/db_connection.php';

    /**
     * Buscar o registo de qualquer tabela através do ID
     *
     * @param PDO $pdo Conexão com a base de dados
     * @param string $table O nome da tabela
     * @param int $id O ID do registo
     * @return array|null Os dados do registo ou null se não encontrado
     */
    function getRecordById(PDO $pdo, string $table, int $id): ?array {

        $allowedTables = ['bolsas_sangue', 'dadores', 'doacoes', 'hospitais', 'transfusoes'];
        if (!in_array($table, $allowedTables)) {
            error_log("Tentativa de acesso a tabela inválida: " . $table);
            return null;
        }

        try {
            $sql = "SELECT * FROM {$table} WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar registo na tabela {$table}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Remover um registo de qualquer tabela através do ID
     *
     * @param PDO $pdo Conexão com a base de dados
     * @param string $table O nome da tabela
     * @param int $id O ID do registo
     * @return bool Retorna true se o registo foi removido com sucesso, false caso contrário
     */
    function deleteRecordById(PDO $pdo, string $table, int $id): bool {

        $allowedTables = ['bolsas_sangue', 'dadores', 'doacoes', 'hospitais', 'transfusoes'];
        if (!in_array($table, $allowedTables)) {
            error_log("Tentativa de acesso a tabela inválida para remoção: " . $table);
            return false;
        }

        try {
            $sql = "DELETE FROM {$table} WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao tentar remover registo na tabela {$table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Função para obter as contagens de dadores e aplicar filtros
     *
     * @param PDO $pdo Conexão com a base de dados
     * @param array $filtros Filtros para aplicar na consulta (tipo_sanguineo, sexo, estado)
     * @return array Contagens e dados dos dadores com filtros aplicados
     */
    function getFilteredDadores(PDO $pdo, array $filtros): array {

        $query_contagens = "
            SELECT 
                SUM(tipo_sanguineo = 'A+') AS countAPositivo,
                SUM(tipo_sanguineo = 'A-') AS countANegativo,
                SUM(tipo_sanguineo = 'B+') AS countBPositivo,
                SUM(tipo_sanguineo = 'B-') AS countBNegativo,
                SUM(tipo_sanguineo = 'AB+') AS countABPositivo,
                SUM(tipo_sanguineo = 'AB-') AS countABNegativo,
                SUM(tipo_sanguineo = 'O+') AS countOPositivo,
                SUM(tipo_sanguineo = 'O-') AS countONegativo,
                SUM(sexo = 'Feminino') AS countFeminino,
                SUM(sexo = 'Masculino') AS countMasculino,
                SUM(estado = 1) AS countAtivo,
                SUM(estado = 0) AS countInativo
            FROM dadores
        ";

        $stmt = $pdo->prepare($query_contagens);
        $stmt->execute();
        $contagens = $stmt->fetch(PDO::FETCH_ASSOC);

        $query_filtro_dadores = "SELECT * FROM dadores WHERE 1=1";
        $params = [];

        if (!empty($filtros['tipo_sanguineo'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['tipo_sanguineo']), '?'));
            $query_filtro_dadores .= " AND tipo_sanguineo IN ($placeholders)";
            $params = array_merge($params, $filtros['tipo_sanguineo']);
        }

        if (!empty($filtros['sexo'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['sexo']), '?'));
            $query_filtro_dadores .= " AND sexo IN ($placeholders)";
            $params = array_merge($params, $filtros['sexo']);
        }

        if (!empty($filtros['estado'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['estado']), '?'));
            $query_filtro_dadores .= " AND estado IN ($placeholders)";
            $params = array_merge($params, $filtros['estado']);
        }

        $query_filtro_dadores .= " ORDER BY nome ASC";

        $stmt = $pdo->prepare($query_filtro_dadores);
        $stmt->execute($params);
        $dadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'contagens' => $contagens,
            'dadores' => $dadores
        ];
    }

    /**
     * Função para obter a lista dos dadores ativos
     *
     * @param PDO $pdo Conexão com a base de dados
     * @return array Lista de dadores ativos
     */
    function getActiveDadores(PDO $pdo): array {

        $query_dadores_ativos = "SELECT id, nome, n_utente FROM dadores WHERE estado = 1 ORDER BY nome ASC";

        $stmt = $pdo->prepare($query_dadores_ativos);
        $stmt->execute();
        $dadoresAtivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'dadoresAtivos' => $dadoresAtivos
        ];
    }

    /**
     * Obtém as doações de uma data específica
     *
     * @param PDO $pdo Conexão ativa com a base de dados
     * @param string|null $dataSelecionada Data a ser filtrada (YYYY-MM-DD); se for nula, assume a data atual
     * @return array Lista de doações do dia, incluindo informações dos dadores
     */
    function getDoacoesByDate(PDO $pdo, ?string $dataSelecionada = null): array {

        if (!$dataSelecionada || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataSelecionada)) {
            $dataSelecionada = date('Y-m-d');
        }

        try {
            $query = "
                SELECT doacoes.*, dadores.nome, dadores.n_utente, dadores.data_nascimento, dadores.tipo_sanguineo, dadores.peso, dadores.sexo
                FROM doacoes
                INNER JOIN dadores ON doacoes.id_dador = dadores.id 
                WHERE doacoes.data = :dataSelecionada
                ORDER BY doacoes.hora ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':dataSelecionada', $dataSelecionada, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erro ao buscar doações: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Função para obter dados de uma função específica: obter a última doação e calcular a idade do dador
     *
     * @param PDO $pdo Conexão com o banco de dados.
     * @param array $doacao Dados da doação específica.
     * @param string $dataSelecionada Data selecionada para comparação.
     * @return array Contendo a idade do dador, última data da doação e se a doação já ocorreu.
     */
    function getDoacaoDetails(PDO $pdo, array $doacao, string $dataSelecionada): array {

        $idade = date_diff(date_create($doacao['data_nascimento']), date_create($dataSelecionada))->y;

        try {

            $sql_ultima_doacao = "
                SELECT COALESCE(MAX(data), NULL) AS ultima_data 
                FROM doacoes 
                WHERE id_dador = :id_dador AND data < :dataSelecionada
            ";

            $stmt = $pdo->prepare($sql_ultima_doacao);
            $stmt->bindParam(':id_dador', $doacao['id_dador'], PDO::PARAM_INT);
            $stmt->bindParam(':dataSelecionada', $dataSelecionada, PDO::PARAM_STR);
            $stmt->execute();
            $ultima_doacao = $stmt->fetchColumn();

        } catch (PDOException $e) {
            error_log("Erro ao buscar última doação: " . $e->getMessage());
            $ultima_doacao = null;
        }
        
        $doacaoDateTime = $doacao['data'] . ' ' . $doacao['hora'];
        $doacaoDateTimeObj = new DateTime($doacaoDateTime);
        $currentDateTime = new DateTime();
        $isPastDoacao = $doacaoDateTimeObj < $currentDateTime;

        return [
            'idade' => $idade,
            'ultima_doacao' => $ultima_doacao,
            'isPastDoacao' => $isPastDoacao
        ];
    }

?>