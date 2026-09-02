<?php 
$pageTitle = "Kampanjan Hallinta - Roolipelisovellus";
require __DIR__ . '/partials/head.php'; 
?>

<div class="campaign-view">
    <div class="view-header">
        <div>
            <h1><?= htmlspecialchars($campaign['campaign_name']); ?></h1>
            <p class="campaign-tagline"><?= htmlspecialchars($campaign['description'] ?? 'Ei kuvausta'); ?></p>
        </div>
        <a href="index.php?action=dashboard" class="btn btn-secondary">← Takaisin</a>
    </div>

    <div class="campaign-info-section">
        <div class="info-card">
            <h3>Kampanjan Tiedot</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Kampajan nimi</label>
                    <span><?= htmlspecialchars($campaign['campaign_name']); ?></span>
                </div>
                <div class="info-item">
                    <label>Kutsukoodi pelaajille</label>
                    <code class="invite-code"><?= $campaign['invite_code']; ?></code>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3>Päivitä Kampanjaa</h3>
            <form action="index.php?action=campaign_update" method="POST" class="campaign-form">
                <input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id']; ?>">
                
                <div class="form-group">
                    <label>Kampanjan nimi</label>
                    <input type="text" name="campaign_name" value="<?= htmlspecialchars($campaign['campaign_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Kuvaus</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($campaign['description'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Tallenna muutokset</button>
            </form>
        </div>
    </div>

    <div class="characters-section">
        <h2>Kampanjan Hahmot (<?= count($players); ?>)</h2>
        
        <?php if (count($players) > 0): ?>
            <div class="characters-table">
                <div class="table-header">
                    <div class="col-player">Pelaaja</div>
                    <div class="col-character">Hahmo</div>
                    <div class="col-race">Rotu / Luokka</div>
                    <div class="col-hp">HP</div>
                </div>

                <?php foreach ($players as $p): ?>
                    <div class="table-row">
                        <div class="col-player"><?= htmlspecialchars($p['player_name']); ?></div>
                        <div class="col-character"><?= htmlspecialchars($p['character_name']); ?></div>
                        <div class="col-race">
                            <span class="race-badge"><?= $p['race_name']; ?></span>
                            <span class="class-badge"><?= $p['class_name']; ?></span>
                        </div>
                        <div class="col-hp">
                            <div class="hp-bar">
                                <div class="hp-fill" style="width: <?= ($p['hp_current'] / $p['hp_max'] * 100); ?>%"></div>
                                <span class="hp-text"><?= $p['hp_current']; ?> / <?= $p['hp_max']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Ei hahmoja kampanjassa</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>