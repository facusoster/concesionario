<section class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="h4 mb-1">FAQ de la App</h2>
        <p class="text-secondary mb-0">Contenido integrado desde README.md para consulta rapida en el sistema.</p>
    </div>
    <a class="btn btn-outline-secondary" href="dashboard.php">Volver al panel</a>
</section>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="faq-content">
            <?php echo $faqHtml; ?>
        </div>
    </div>
</div>

<style>
    .faq-content h2 {
        font-size: 1.25rem;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        color: #0f172a;
    }

    .faq-content h3 {
        font-size: 1.05rem;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: #1e3a8a;
    }

    .faq-content p,
    .faq-content li {
        color: #374151;
    }

    .faq-content code {
        background: #eef4ff;
        color: #1d4ed8;
        padding: 1px 6px;
        border-radius: 6px;
        font-size: 0.9em;
    }

    .faq-content ul {
        margin-bottom: 0.75rem;
    }
</style>
