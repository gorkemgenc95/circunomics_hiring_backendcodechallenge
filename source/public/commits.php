<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ServiceContainer;

$container = ServiceContainer::getInstance();
$queryService = $container->getCommitQueryService();

$page = (int) ($_GET['page'] ?? 1);
$perPage = 20;
$platform = $_GET['platform'] ?? 'github';
$owner = $_GET['owner'] ?? null;
$repo = $_GET['repo'] ?? null; if (!in_array($platform, ['github', 'gitlab', 'bitbucket'])) $platform = 'github';

$page = max(1, $page);

try {
    $result = $queryService->getPaginatedCommits($page, $perPage, $platform, $owner, $repo);
    $commits = $result['commits'];
    $pagination = $result['pagination'];
} catch (Exception $e) {
    $error = $e->getMessage();
    $commits = [];
    $pagination = [
        'current_page' => 1,
        'total_pages' => 0,
        'total_commits' => 0,
        'has_previous' => false,
        'has_next' => false,
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git Platform Commits</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f6f8fa;
            line-height: 1.6;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #e1e4e8;
            padding-bottom: 20px;
        }
        h1 {
            color: #24292e;
            margin: 0;
        }
        .home-link {
            background: #f6f8fa;
            color: #24292e;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #d1d5da;
            transition: background-color 0.2s;
        }
        .home-link:hover {
            background: #e1e4e8;
        }
        .filters {
            background: #f6f8fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .filters form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filters input, .filters select {
            padding: 8px 12px;
            border: 1px solid #d1d5da;
            border-radius: 4px;
        }
        .filters button {
            background: #0366d6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .filters button:hover {
            background: #0256cc;
        }
        .clear-filters {
            background: #6a737d;
            margin-left: 10px;
        }
        .clear-filters:hover {
            background: #586069;
        }
        .stats {
            background: #f1f8ff;
            border: 1px solid #c8e1ff;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .table-container {
            overflow-x: auto;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th {
            background: #f6f8fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #24292e;
            border-bottom: 1px solid #e1e4e8;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #f6f8fa;
            vertical-align: top;
        }
        tr:hover {
            background: #f6f8fa;
        }
        .commit-hash {
            font-family: 'SFMono-Regular', Consolas, monospace;
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        .repository {
            color: #0366d6;
            font-weight: 500;
        }
        .date {
            color: #586069;
            font-size: 14px;
        }
        .platform {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .platform.gitlab {
            background: #fc6d26;
        }
        .platform.bitbucket {
            background: #0052cc;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #d1d5da;
            border-radius: 4px;
            color: #24292e;
        }
        .pagination a:hover {
            background: #f6f8fa;
        }
        .pagination .current {
            background: #0366d6;
            color: white;
            border-color: #0366d6;
        }
        .pagination .disabled {
            color: #959da5;
            background: #f6f8fa;
        }
        .error {
            background: #ffeef0;
            border: 1px solid #f0b8c8;
            color: #d73a49;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #586069;
        }
        .empty-state h3 {
            color: #24292e;
            margin-bottom: 10px;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'SFMono-Regular', Consolas, monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Git Platform Commits</h1>
            <a href="/" class="home-link">← Home</a>
        </div>

        <div class="filters">
            <form method="GET">
                <label>Platform:</label>
                <label>
                    <select name="platform">
                        <option value="github"<?= $platform === 'github' ? ' selected' : '' ?>>GitHub</option>
                        <option value="gitlab"<?= $platform === 'gitlab' ? ' selected' : '' ?>>GitLab</option>
                        <option value="bitbucket"<?= $platform === 'bitbucket' ? ' selected' : '' ?>>Bitbucket</option>
                    </select>
                </label>

                <label>Filter by repository:</label>
                <label>
                    <input type="text" name="owner" placeholder="Owner (e.g., nodejs)" value="<?= htmlspecialchars($owner ?? '') ?>">
                </label>
                <span>/</span>
                <label>
                    <input type="text" name="repo" placeholder="Repository (e.g., node)" value="<?= htmlspecialchars($repo ?? '') ?>">
                </label>
                <button type="submit">Filter</button>
                <a href="/commits" class="filters button clear-filters">Clear Filters</a>
            </form>
        </div>

        <?php if (isset($error)): ?>
            <div class="error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php else: ?>
            <div class="stats">
                <strong>Platform:</strong> <?= ucfirst(htmlspecialchars($platform)) ?> | <strong>Total Commits:</strong> <?= number_format($pagination['total_commits']) ?>
                <?php if ($owner && $repo): ?>
                    | <strong>Repository:</strong> <?= htmlspecialchars("$owner/$repo") ?>
                <?php endif; ?>
                | <strong>Page:</strong> <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?>
            </div>

            <?php if (empty($commits)): ?>
                <div class="empty-state">
                    <h3>No commits found</h3>
                    <?php if ($owner && $repo): ?>
                        <p>No commits found for repository <strong><?= htmlspecialchars("$owner/$repo") ?></strong>.</p>
                        <p>Try syncing commits first: <code>make sync-commits OWNER=<?= htmlspecialchars($owner) ?> REPO=<?= htmlspecialchars($repo) ?></code></p>
                    <?php else: ?>
                        <p>No commits in database. Try syncing some commits first:</p>
                        <p><code>make sync-commits OWNER=nodejs REPO=node</code></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Hash</th>
                                <th>Author</th>
                                <th>Repository Owner</th>
                                <th>Repository Name</th>
                                <th>Platform</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commits as $commit): ?>
                                <tr>
                                    <td>
                                        <span class="commit-hash">
                                            <?= htmlspecialchars(substr($commit['hash'], 0, 7)) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($commit['author']) ?></td>
                                    <td>
                                        <span class="repository">
                                            <?= htmlspecialchars($commit['repository_owner']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="repository">
                                            <?= htmlspecialchars($commit['repository_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="platform <?= htmlspecialchars(strtolower($commit['platform'] ?? 'github')) ?>">
                                            <?= htmlspecialchars($commit['platform'] ?? 'github') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="date">
                                            <?= htmlspecialchars($commit['date']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php
                        $baseUrl = '/commits';
                        $queryParams = [];
                        if ($platform !== 'github') $queryParams['platform'] = $platform;
                        if ($owner) $queryParams['owner'] = $owner;
                        if ($repo) $queryParams['repo'] = $repo;
                        
                        function buildUrl($baseUrl, $params, $page) {
                            $params['page'] = $page;
                            return $baseUrl . '?' . http_build_query($params);
                        }
                        ?>
                        
                        <?php if ($pagination['has_previous']): ?>
                            <a href="<?= htmlspecialchars(buildUrl($baseUrl, $queryParams, 1)) ?>">First</a>
                            <a href="<?= htmlspecialchars(buildUrl($baseUrl, $queryParams, $pagination['current_page'] - 1)) ?>">Previous</a>
                        <?php else: ?>
                            <span class="disabled">First</span>
                            <span class="disabled">Previous</span>
                        <?php endif; ?>

                        <span class="current">
                            Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?>
                        </span>

                        <?php if ($pagination['has_next']): ?>
                            <a href="<?= htmlspecialchars(buildUrl($baseUrl, $queryParams, $pagination['current_page'] + 1)) ?>">Next</a>
                            <a href="<?= htmlspecialchars(buildUrl($baseUrl, $queryParams, $pagination['total_pages'])) ?>">Last</a>
                        <?php else: ?>
                            <span class="disabled">Next</span>
                            <span class="disabled">Last</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html> 