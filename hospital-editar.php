<?php

    include 'partials/header.php';
    include 'includes/obter_registo.php';

    $hospital = $record;

    $pageTitle = "Editar Hospital";
    $breadcrumbItems = [
        ['title' => 'Dashboard', 'url' => 'index.php', 'active' => false],
        ['title' => 'Hospitais', 'url' => 'hospitais.php', 'active' => false],
        ['title' => 'Editar Hospital', 'url' => '#', 'active' => true]
    ];

?>

<div class="container p-4">
    <?php include 'partials/page-header.php'; ?>
    
    <div class="container d-flex justify-content-center align-items-start">
        <div class="card border-0 shadow-lg" style="width: 42rem; border-radius: 20px; background-color: #ffffff;">
            <!-- Header do Formulário -->
            <div class="card-header border-0 text-center p-4" style="background-color: #f8f9fa; border-radius: 20px 20px 0 0; border-bottom: 1px solid #dee2e6;">
                <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 60px; height: 60px; background-color: #202d3b;">
                    <i class="fa-solid fa-edit text-white" style="font-size: 1.5rem;"></i>
                </div>
                <h4 class="text-dark mb-1">Editar Hospital</h4>
                <p class="text-muted mb-0">Atualize os dados do hospital <?php echo $hospital['nome']; ?></p>
            </div>
            
            <form method="POST" action="includes/update.php">
                <div class="card-body p-4">
                    <input type="hidden" name="table" value="hospitais">
                    <input type="hidden" name="id" value="<?php echo $hospital['id']; ?>">
                    
                    <!-- Nome do Hospital -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                <i class="fa-regular fa-hospital text-white"></i>
                            </span>
                            <input type="text" class="form-control py-3 px-3 border-0 rounded-end-3" id="nome" name="nome" value="<?php echo $hospital['nome']; ?>" placeholder="Nome do hospital" required style="background-color: #f8f9fa; transition: all 0.3s ease;">
                        </div>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                <i class="fa-solid fa-location-dot text-white"></i>
                            </span>
                            <input type="text" class="form-control py-3 px-3 border-0 rounded-end-3" id="endereco" name="endereco" value="<?php echo $hospital['endereco']; ?>" placeholder="Endereço completo" required style="background-color: #f8f9fa; transition: all 0.3s ease;">
                        </div>
                    </div>
                    
                    <!-- Telefone e Email -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                    <i class="fa-solid fa-phone text-white"></i>
                                </span>
                                <input type="text" class="form-control py-3 px-3 border-0 rounded-end-3" id="telefone" name="telefone" value="<?php echo $hospital['telefone']; ?>" placeholder="(00) 0000-0000" required style="background-color: #f8f9fa; transition: all 0.3s ease;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                    <i class="fa-solid fa-envelope text-white"></i>
                                </span>
                                <input type="email" class="form-control py-3 px-3 border-0 rounded-end-3" id="email" name="email" value="<?php echo $hospital['email']; ?>" placeholder="hospital@exemplo.com" required style="background-color: #f8f9fa; transition: all 0.3s ease;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Responsável -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3" style="background: linear-gradient(135deg, #202d3b 0%, #2c3e50 100%);">
                                <i class="fa-solid fa-user-tie text-white"></i>
                            </span>
                            <input type="text" class="form-control py-3 px-3 border-0 rounded-end-3" id="nome_responsavel" name="nome_responsavel" value="<?php echo $hospital['nome_responsavel']; ?>" placeholder="Nome do responsável" required style="background-color: #f8f9fa; transition: all 0.3s ease;">
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="mb-4">
                        <div class="form-check p-3 rounded-3" style="background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%); border: 2px solid #dee2e6;">
                            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1" <?php echo $hospital['estado'] == 1 ? 'checked' : ''; ?> style="transform: scale(1.3); accent-color: #198754;">
                            <label class="form-check-label text-dark fw-medium ms-2" for="estado">
                                <i class="fa-solid fa-check-circle text-success me-2"></i>Hospital ativo no sistema
                            </label>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="d-flex gap-3">
                        <a href="hospitais.php" class="btn btn-light border flex-fill py-3 rounded-3 fw-medium" style="transition: all 0.3s ease;">
                            <i class="fa-solid fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn text-white flex-fill py-3 rounded-3 fw-medium" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); transition: all 0.3s ease;">
                            <i class="fa-solid fa-save me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    box-shadow: 0 0 0 3px rgba(32, 45, 59, 0.1);
    background-color: white !important;
    transform: translateY(-2px);
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.form-check:hover {
    transform: translateX(5px);
    border-color: #198754 !important;
}

.input-group:hover .form-control {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(32, 45, 59, 0.15) !important;
}

/* Animação para os ícones */
.input-group-text i {
    transition: all 0.3s ease;
}

.input-group:hover .input-group-text i {
    transform: scale(1.1);
}
</style>

<?php include 'partials/footer.php'; ?>

