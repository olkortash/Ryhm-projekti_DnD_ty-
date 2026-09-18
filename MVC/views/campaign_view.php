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
                    <label>Invite Code</label>
                    <code class="invite-code"><?= $campaign['invite_code']; ?></code>
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
                    <button type="submit" name="delete_campaign" value="1" formaction="index.php?action=campaign_delete&redirect=dashboard" class="btn btn-danger" onclick="return confirm('Haluatko varmasti poistaa tämän kampanjan? Tämä toiminto on peruuttamaton.');">Delete Campaign</button>
                </div>
            </form>
            </div>
        <?php else: ?>
            <div class="info-card campaign-join-note">
                <h3>Join Campaign</h3>
                <p>Add your character to this campaign using the invite code on the character details page.</p>
                <a href="index.php?action=dashboard" class="btn btn-secondary">View my characters</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="characters-section">
        <h2>Campaign members</h2>

        <?php if ($isGm): ?>
            <div class="campaign-member-list">
                <?php foreach ($campaignMembers as $member): ?>
                    <div class="member-row">
                        <div class="member-info">
                            <strong><?= htmlspecialchars($member['username']); ?></strong>
                            <span class="role-badge <?= $member['role'] === 'Game Master' ? 'role-gm' : 'role-player'; ?>"><?= htmlspecialchars($member['role']); ?></span>
                        </div>

                        <?php if ((int)$member['user_id'] !== (int)$campaign['gm_id']): ?>
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
                        <?php else: ?>
                            <span class="member-owner-note">Campaign creator</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <form action="index.php?action=campaign_members_update" method="POST" class="campaign-form member-add-form">
                <input type="hidden" name="campaign_id" value="<?= (int)$campaign['campaign_id']; ?>">

                <div class="form-group">
                    <label>Add player to campaign</label>
                    <select name="user_id" required>
                        <option value="">Select a player</option>
                        <?php foreach ($availableUsers as $user): ?>
                            <option value="<?= (int)$user['user_id']; ?>"><?= htmlspecialchars($user['username']); ?></option>
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

    <div class="characters-section">
        <h2>Campaign Characters (<?= count($players); ?>)</h2>

        <?php if (count($players) > 0): ?>
            <div class="characters-table">
                <div class="table-header">
                    <div class="col-player">Player</div>
                    <div class="col-character">Character</div>
                    <div class="col-race">Race / Class</div>
                    <div class="col-hp">HP</div>
                </div>

                <?php foreach ($players as $p): ?>
                    <div class="table-row">
                        <div class="col-player"><?= htmlspecialchars($p['player_name']); ?></div>
                        <div class="col-character">
                            <?= htmlspecialchars($p['character_name']); ?>
                            <?php if ($isGm): ?>
                                <form action="index.php?action=campaign_remove_character" method="POST" onsubmit="return confirm('Remove this character from the campaign?');">
                                    <input type="hidden" name="character_id" value="<?= $p['character_id']; ?>">
                                    <input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id']; ?>">
                                    <button type="submit" class="btn btn-danger compact">Remove</button>
                                </form>
                            <?php endif; ?>
                        </div>
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
                <p>No characters in campaign</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isGm): ?>
        <div class="campaign-info-section">
            <div class="info-card">
                <h3>Session notes and attendance</h3>

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
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>