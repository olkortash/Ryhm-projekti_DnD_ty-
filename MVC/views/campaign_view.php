<?php
/*
 * Kampanjasivu: CampaignController välittää kampanjan, jäsenet, hahmot ja tiedotteet.
 * $isGm tarkoittaa kampanjan luojaa; $canViewPrivate sallii jäsenten ja luojan yksityiset osiot.
 * Näkymän ehdot ohjaavat näkyvyyttä, ja controller käsittelee lomakkeiden toimintopyynnöt.
 */
$pageTitle = "Campaign management - Roleplay App";
require __DIR__ . '/partials/head.php';
?>

<div class="campaign-view">
    <div class="view-header">
        <div>
            <h1><?= htmlspecialchars($campaign['campaign_name']); ?></h1>
            <p class="campaign-tagline"><?= htmlspecialchars($campaign['description'] ?? 'Ei kuvausta'); ?></p>
        </div>
        <a href="index.php?action=dashboard" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="campaign-info-section">
        <div class="info-card">
            <h3>Campaign Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Campaign Name</label>
                    <span><?= htmlspecialchars($campaign['campaign_name']); ?></span>
                </div>
                <div class="info-item">
                    <?php if ($isGm): ?>
                        <label>Invite Code</label>
                        <code class="invite-code"><?= htmlspecialchars($campaign['invite_code']); ?></code>
                    <?php else: ?>
                        <label>Access</label>
                        <span>Public campaign</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($isGm): ?>
            <div class="info-card">
                <h3>Update Campaign</h3>
                <form action="index.php?action=campaign_update&redirect=dashboard" method="POST" class="campaign-form">
                    <input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id']; ?>">

                    <div class="form-group">
                        <label>Campaign Name</label>
                        <input type="text" name="campaign_name" value="<?= htmlspecialchars($campaign['campaign_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?= htmlspecialchars($campaign['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button
                            type="submit"
                            name="delete_campaign"
                            value="1"
                            formaction="index.php?action=campaign_delete&redirect=dashboard"
                            class="btn btn-danger"
                            onclick="return confirm('Haluatko varmasti poistaa tämän kampanjan? Tämä toiminto on peruuttamaton.');"
                        >Delete Campaign</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="info-card campaign-join-note">
                <h3>Join Campaign</h3>
                <?php if ($alreadyJoined): ?>
                    <p>Already joined</p>
                <?php elseif (!empty($joinableCharacters)): ?>
                    <p>Select a character to join this public campaign.</p>
                    <form action="index.php?action=campaign_join_public" method="POST" class="campaign-form">
                        <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                        <div class="form-group">
                            <label for="join-character">Character</label>
                            <select id="join-character" name="character_id" required>
                                <option value="">Select a character</option>
                                <?php foreach ($joinableCharacters as $character): ?>
                                    <option value="<?= (int)$character['character_id']; ?>">
                                        <?= htmlspecialchars($character['character_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Join campaign</button>
                    </form>
                <?php else: ?>
                    <p>You do not have an available character to join with.</p>
                    <a href="index.php?action=dashboard" class="btn btn-secondary">View my characters</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php // Vain kampanjan jäsenet ja luoja näkevät tästä alkavat tiedotteet, jäsenet ja pelisessiot. ?>
    <?php if ($canViewPrivate): ?>
        <section class="characters-section campaign-roster-section" aria-labelledby="campaign-characters-title">
            <div class="campaign-section-heading">
                <p class="eyebrow">Campaign workspace</p>
                <h2 id="campaign-characters-title">Campaign Characters <span>(<?= count($players); ?>)</span></h2>
                <p>Review your party, roll initiative, and manage character levels.</p>
            </div>

            <?php if (count($players) > 0): ?>
                <div class="characters-table campaign-roster <?= $isGm ? 'campaign-roster-gm' : ''; ?>">
                    <div class="table-header" aria-hidden="true">
                        <div>Player</div>
                        <div>Character</div>
                        <div>Level</div>
                        <div>Roll</div>
                        <div>Race / Class</div>
                        <div>HP</div>
                        <?php if ($isGm): ?><div>Actions</div><?php endif; ?>
                    </div>

                    <?php foreach ($players as $p): ?>
                        <?php
                        $hpMax = max(1, (int)($p['hp_max'] ?? 1));
                        $hpCurrent = max(0, (int)($p['hp_current'] ?? 0));
                        $hpPercent = min(100, ($hpCurrent / $hpMax) * 100);
                        $level = max(1, (int)($p['level'] ?? 1));
                        ?>
                        <div class="table-row">
                            <div class="col-player" data-label="Player"><?= e($p['player_name']); ?></div>
                            <div class="col-character" data-label="Character">
                                <a class="character-profile-link" href="index.php?action=character_view&amp;id=<?= (int)$p['character_id']; ?>">
                                    <?= e($p['character_name']); ?>
                                </a>
                            </div>
                            <div class="col-level" data-label="Level">
                                <strong><?= $level; ?></strong>
                                <?php if ($isGm): ?>
                                    <?php if ($level < 20): ?>
                                        <form action="index.php?action=campaign_level_up" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                                            <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                                            <input type="hidden" name="character_id" value="<?= (int)$p['character_id']; ?>">
                                            <button type="submit" class="btn btn-secondary compact" aria-label="Raise <?= e($p['character_name']); ?> to level <?= $level + 1; ?>">Level up</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="level-cap-note">Max level</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-dice" data-label="Roll">
                                <div class="dice-roller">
                                    <button type="button" class="d20-button" onclick="rollD20(this)">Roll</button>
                                    <span class="d20-result">-</span>
                                </div>
                            </div>
                            <div class="col-race" data-label="Race / Class">
                                <span class="race-badge"><?= e($p['race_name'] ?? 'Unknown'); ?></span>
                                <span class="class-badge"><?= e($p['class_name'] ?? 'Unknown'); ?></span>
                            </div>
                            <div class="col-hp" data-label="HP">
                                <div class="hp-bar">
                                    <div class="hp-fill" style="width: <?= $hpPercent; ?>%"></div>
                                    <span class="hp-text"><?= $hpCurrent; ?> / <?= $hpMax; ?></span>
                                </div>
                            </div>
                            <?php if ($isGm): ?>
                                <div class="col-actions" data-label="Actions">
                                    <form action="index.php?action=campaign_remove_character" method="POST" onsubmit="return confirm('Remove this character from the campaign?');">
                                        <input type="hidden" name="character_id" value="<?= (int)$p['character_id']; ?>">
                                        <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                                        <button type="submit" class="btn btn-danger compact">Remove</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No characters in campaign</p>
                </div>
            <?php endif; ?>
        </section>

        <div class="campaign-info-section campaign-single-section">
            <div class="info-card">
                <h3>Campaign announcements</h3>

                <?php if ($isGm): ?>
                    <?php // Sama tallennusreitti luo tiedotteen tai päivittää announcement_id-kentällä valitun tiedotteen. ?>
                    <form action="index.php?action=campaign_announcement_save" method="POST" class="campaign-form">
                        <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                        <div class="form-group">
                            <label for="announcement-title">Title</label>
                            <input id="announcement-title" type="text" name="announcement_title" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label for="announcement-body">Message</label>
                            <textarea id="announcement-body" name="announcement_body" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Publish announcement</button>
                    </form>
                <?php endif; ?>

                <?php if (empty($announcements)): ?>
                    <p class="empty-state">No announcements yet.</p>
                <?php else: ?>
                    <div class="announcement-list">
                        <?php foreach ($announcements as $announcement): ?>
                            <article class="announcement-card">
                                <div class="session-header">
                                    <h4><?= e($announcement['title']); ?></h4>
                                    <small><?= e($announcement['author_name']); ?> · <?= e($announcement['created_at']); ?></small>
                                </div>
                                <p><?= nl2br(e($announcement['body'])); ?></p>
                                <?php if ($isGm): ?>
                                    <details>
                                        <summary>Edit announcement</summary>
                                        <form action="index.php?action=campaign_announcement_save" method="POST" class="campaign-form">
                                            <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                                            <input type="hidden" name="announcement_id" value="<?= (int)$announcement['announcement_id']; ?>">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" name="announcement_title" maxlength="255" value="<?= e($announcement['title']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Message</label>
                                                <textarea name="announcement_body" rows="4" required><?= e($announcement['body']); ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-secondary">Save changes</button>
                                        </form>
                                        <form action="index.php?action=campaign_announcement_delete" method="POST" onsubmit="return confirm('Delete this announcement?');">
                                            <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                                            <input type="hidden" name="announcement_id" value="<?= (int)$announcement['announcement_id']; ?>">
                                            <button type="submit" class="btn btn-danger compact">Delete announcement</button>
                                        </form>
                                    </details>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="characters-section">
            <h2>Campaign members</h2>

            <div class="campaign-member-list">
                <?php foreach ($campaignMembers as $member): ?>
                    <div class="member-row">
                        <div class="member-info">
                            <strong><?= htmlspecialchars($member['username']); ?></strong>
                            <span class="role-badge <?= $member['role'] === 'Game Master' ? 'role-gm' : 'role-player'; ?>"><?= htmlspecialchars($member['role']); ?></span>
                        </div>

                        <?php // Kampanjan luojan omaa jäsenyyttä ei tarjota muokattavaksi tai poistettavaksi. ?>
                        <?php if ($isGm && (int)$member['user_id'] !== (int)$campaign['gm_id']): ?>
                            <form action="index.php?action=campaign_members_update" method="POST" class="member-form">
                                <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$member['user_id']; ?>">

                                <select name="member_role">
                                    <option value="Player" <?= $member['role'] === 'Player' ? 'selected' : ''; ?>>Player</option>
                                    <option value="Game Master" <?= $member['role'] === 'Game Master' ? 'selected' : ''; ?>>Game Master</option>
                                </select>

                                <div class="member-actions">
                                    <button type="submit" name="update_member_role" value="1" class="btn btn-primary">Save role</button>
                                    <button type="submit" name="remove_member" value="1" class="btn btn-danger" onclick="return confirm('Remove this player from campaign?');">Remove</button>
                                </div>
                            </form>
                        <?php elseif ($isGm): ?>
                            <span class="member-owner-note">Campaign creator</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($isGm): ?>
                <form action="index.php?action=campaign_members_update" method="POST" class="campaign-form member-add-form">
                    <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">

                    <div class="form-group">
                        <label>Player character</label>
                        <select name="character_id" required>
                            <option value="">Select a character</option>
                            <?php foreach ($availableCharacters as $character): ?>
                                <option value="<?= (int)$character['character_id']; ?>">
                                    <?= htmlspecialchars($character['username'] . ' - ' . $character['character_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select name="member_role">
                            <option value="Player" selected>Player</option>
                            <option value="Game Master">Game Master</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="add_member" value="1" class="btn btn-primary">Add player</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($isGm): ?>
            <div class="campaign-info-section campaign-single-section">
                <div class="info-card">
                    <h3>Session notes and attendance</h3>

                    <?php // attendees[] välittää valittujen osallistujien tunnisteet session tallennukseen. ?>
                    <form action="index.php?action=campaign_session_save" method="POST" class="campaign-form">
                        <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">

                        <div class="form-group">
                            <label>Session date</label>
                            <input type="date" name="session_date" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Session title</label>
                            <input type="text" name="session_title" placeholder="Example: Session 3 - Trollmire" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label>Session notes</label>
                            <textarea name="session_summary" rows="5" placeholder="Write down the most important events, decisions and plot changes..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Participants</label>
                            <div class="checkbox-list">
                                <?php if (empty($players)): ?>
                                    <p>No participants to track yet.</p>
                                <?php else: ?>
                                    <?php foreach ($players as $player): ?>
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="attendees[]" value="<?= (int)$player['player_id']; ?>">
                                            <?= htmlspecialchars($player['player_name']); ?> / <?= htmlspecialchars($player['character_name']); ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save session</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="characters-section">
            <h2>Saved sessions</h2>

            <?php if (empty($sessionNotes)): ?>
                <div class="empty-state">
                    <p>No session notes yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($sessionNotes as $session): ?>
                    <div class="info-card session-card">
                        <div class="session-header">
                            <h3><?= htmlspecialchars($session['title']); ?></h3>
                            <span><?= htmlspecialchars(date('d.m.Y', strtotime($session['session_date']))); ?></span>
                        </div>

                        <?php if (!empty($session['summary'])): ?>
                            <p><?= nl2br(htmlspecialchars($session['summary'])); ?></p>
                        <?php endif; ?>

                        <p>
                            <strong>Participants:</strong>
                            <?= !empty($session['attendee_names']) ? htmlspecialchars($session['attendee_names']) : 'No recorded attendance'; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
