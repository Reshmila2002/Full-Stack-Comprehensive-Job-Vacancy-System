<?php
// companies/index.php
require_once '../config/db_connect.php';

// Read all companies from the database
$stmt = $pdo->query("SELECT * FROM company_profiles ORDER BY company_name ASC");
$companies = $stmt->fetchAll();

include '../includes/header.php'; 
?>

<style>
    .directory-wrapper { padding: 5rem 5%; min-height: 75vh; background-color: #f8fafc; }
    .directory-header { text-align: center; margin-bottom: 4rem; }
    .directory-header h2 { font-size: 2.8rem; color: #1e293b; font-weight: 800; margin-bottom: 0.5rem; }
    .directory-header p { color: #64748b; font-size: 1.1rem; }
    
    .companies-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto; }
    .company-card { background: var(--white); padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); text-align: center; transition: transform 0.3s ease; border: 1px solid #f1f5f9; }
    .company-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    
    .company-logo-placeholder { width: 80px; height: 80px; background: #e0f2fe; color: #0284c7; font-size: 2.5rem; font-weight: bold; display: flex; align-items: center; justify-content: center; border-radius: 20px; margin: 0 auto 1.5rem auto; }
    .company-name { font-size: 1.5rem; color: #0f172a; margin-bottom: 0.5rem; font-weight: 700; }
    .company-industry { color: #64748b; font-weight: 600; font-size: 0.95rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1px; }
    .btn-view { background: white; color: var(--primary-color); border: 2px solid var(--primary-color); padding: 10px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; }
    .btn-view:hover { background: var(--primary-color); color: white; }
</style>

<main class="directory-wrapper">
    <div class="directory-header">
        <h2>Company Directory</h2>
        <p>Discover top employers and explore their open opportunities.</p>
    </div>

    <div class="companies-grid">
        <?php if(count($companies) > 0): ?>
            <?php foreach($companies as $company): ?>
                <div class="company-card">
                    <div class="company-logo-placeholder">
                        <?php echo strtoupper(substr($company['company_name'], 0, 1)); ?>
                    </div>
                    <h3 class="company-name"><?php echo htmlspecialchars($company['company_name']); ?></h3>
                    <p class="company-industry"><?php echo htmlspecialchars($company['industry']); ?></p>
                    <a href="details.php?id=<?php echo $company['id']; ?>" class="btn-view">View Profile</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; color: #64748b; padding: 3rem;">
                <h3>No companies have set up their public profiles yet.</h3>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>