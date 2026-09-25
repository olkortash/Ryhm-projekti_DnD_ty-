<?php
// Suoritus: php MVC/tests/search_test.php. Käyttää vain muistinvaraista SQLite-testikantaa.
require_once __DIR__ . '/../controllers/searchController.php';

function check($condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE users (user_id INTEGER PRIMARY KEY, username TEXT, email TEXT, password_hash TEXT)');
$pdo->exec('CREATE TABLE campaigns (campaign_id INTEGER PRIMARY KEY, campaign_name TEXT, description TEXT, invite_code TEXT)');
$insertUser = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
foreach (['Alice', 'Malice', '100%Mage', 'under_score', 'wow!hero', '<script>alert(1)</script>'] as $name) {
    $insertUser->execute([$name, 'private@example.test', 'private-password-hash']);
}
$insertCampaign = $pdo->prepare('INSERT INTO campaigns (campaign_name, description, invite_code) VALUES (?, ?, ?)');
$insertCampaign->execute(['Dragon cave', 'Find the crystal', 'PRIVATE-CODE']);
$insertCampaign->execute(['Crystal sea', null, 'PRIVATE-CODE']);
$insertCampaign->execute(['100%_!', '<script>alert(1)</script>', 'PRIVATE-CODE']);
$users = new User($pdo);
$campaigns = new Campaign($pdo);

check(count($users->searchByUsername('ali')) === 2, 'Username substring search failed');
check(count($campaigns->searchPublicCampaigns('crystal')) === 2, 'Name/description search failed');
foreach (['%', '_', '!'] as $literal) {
    check(count($users->searchByUsername($literal)) === 1, 'User LIKE escaping failed');
    check(count($campaigns->searchPublicCampaigns($literal)) === 1, 'Campaign LIKE escaping failed');
}
check($users->searchByUsername("' OR 1=1 --") === [], 'User query was not parameterized');
check($campaigns->searchPublicCampaigns("' OR 1=1 --") === [], 'Campaign query was not parameterized');
check(array_keys($users->searchByUsername('Alice')[0]) === ['username'], 'Private user fields returned');
check(!array_key_exists('invite_code', $campaigns->searchPublicCampaigns('Dragon')[0]), 'Invite code returned');

function renderSearch($pdo, $query, bool $loggedIn = false): string {
    $_GET = ['q' => $query];
    $_SESSION = $loggedIn ? ['user_id' => 1] : [];
    ob_start();
    (new SearchController($pdo))->index();
    return ob_get_clean();
}

// Tyhjä tai virheellinen haku ei saa käynnistää tietokantakyselyitä.
$noQueries = new class {
    public function prepare($sql) { throw new RuntimeException('Unexpected database query'); }
};
check(str_contains(renderSearch($noQueries, '  '), 'Use the search field'), 'Empty search guidance missing');
foreach ([['invalid'], str_repeat('x', 101), "\xff"] as $invalid) {
    check(str_contains(renderSearch($noQueries, $invalid), 'Enter a search term'), 'Invalid query not rejected');
}
check(str_contains(renderSearch($pdo, 'no-such-result'), 'No users found.'), 'Empty users message missing');
check(str_contains(renderSearch($pdo, 'no-such-result'), 'No campaigns found.'), 'Empty campaigns message missing');
$guestHtml = renderSearch($pdo, 'crystal');
$memberHtml = renderSearch($pdo, 'crystal', true);
check(str_contains($guestHtml, 'Sign in to view campaign'), 'Guest campaign link missing');
check(str_contains($memberHtml, 'action=campaign_view&amp;id='), 'Member campaign link missing');
$escapedHtml = renderSearch($pdo, '<script>');
check(!str_contains($escapedHtml, '<script>alert(1)</script>'), 'Unsafe search result output');
check(str_contains($escapedHtml, '&lt;script&gt;'), 'Search term/result not escaped');
check(!str_contains($escapedHtml, 'private@example.test'), 'Email exposed');
check(!str_contains($guestHtml, 'PRIVATE-CODE'), 'Invite code exposed');

for ($i = 0; $i < 25; $i++) {
    $insertUser->execute(['Limit user ' . $i, '', '']);
    $insertCampaign->execute(['Limit campaign ' . $i, '', '']);
}
check(count($users->searchByUsername('Limit')) === 21, 'User query must fetch one overflow row');
check(count($campaigns->searchPublicCampaigns('Limit')) === 21, 'Campaign query must fetch one overflow row');
$limitedHtml = renderSearch($pdo, 'Limit');
check(substr_count($limitedHtml, '<li class="search-result-card">') === 40, 'Displayed result limit failed');
check(str_contains($limitedHtml, 'Users (20+)'), 'User overflow indication missing');
check(str_contains($limitedHtml, 'Campaigns (20+)'), 'Campaign overflow indication missing');

echo "Search tests passed: matching, literal wildcards, input validation, escaping, access links and result limits.\n";
