<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../data_src/api/includes/db_connect.php";
session_start();
?>

<!DOCTYPE html>
<html>
<header class="site-header">
    <div class="site-title-container">
        <h1 class="site-title">Fantasy Stocks</h1>
    </div>

    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="leagues.php">League</a></li>
            <li><a href="stocks.php">Stocks</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<head> 
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="stylesheets/styles.css" />
  <link rel="stylesheet" href="stylesheets/stocks.css" />
  <title>Stocks!</title>
</head>
<body>
    <h1>Stocks in the NASDAQ 100 available to Players</h1>
    
    <div class="stocks-list-container">
        <div class="stocks-controls">
            <div class="control-left">
                <label for="stockSearch">Search:</label>
                <input id="stockSearch" type="search" placeholder="Search ticker, name or sector">
            </div>
            <div class="control-right">
                <label for="perPage">Per page:</label>
                <select id="perPage">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="stocks-table-wrapper">
            <table class="stocks-table" id="stocksTable">
                <thead>
                    <tr>
                        <th>Ticker</th>
                        <th>Name</th>
                        <th>Sector</th>
                        <th>Current Price of the Stocks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to get all stocks
                    $sql = "SELECT h.ticker, h.st_name, s.sectorName, h.curr_price 
                            FROM Holdings h
                            JOIN Sector s ON h.index = s.index";
                    $result = $connection->query($sql);

                    if ($result->num_rows > 0) {
                        // Loop through each row and display it
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['ticker']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['st_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sectorName']) . "</td>";
                            echo "<td>$" . number_format($row['curr_price'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No stocks found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div id="pagination" class="pagination"></div>
        </div>
    </div>
    
    <script>
    // Client-side search + pagination (simple, beginner-friendly)
    (function(){
        const table = document.getElementById('stocksTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const search = document.getElementById('stockSearch');
        const perPageSelect = document.getElementById('perPage');
        const pagination = document.getElementById('pagination');

        let filtered = rows.slice();
        let currentPage = 1;

        function renderPage(){
            const perPage = Number(perPageSelect.value);
            const total = filtered.length;
            const pages = Math.max(1, Math.ceil(total / perPage));
            if (currentPage > pages) currentPage = pages;

            // hide all
            rows.forEach(r => r.style.display = 'none');

            // show slice
            const start = (currentPage - 1) * perPage;
            const slice = filtered.slice(start, start + perPage);
            slice.forEach(r => r.style.display = 'table-row');

            // build pagination controls
            pagination.innerHTML = '';
            if (pages <= 1) return;

            const prev = document.createElement('button');
            prev.textContent = 'Prev';
            prev.disabled = currentPage === 1;
            prev.onclick = () => { currentPage--; renderPage(); };
            pagination.appendChild(prev);

            for (let p = 1; p <= pages; p++){
                const btn = document.createElement('button');
                btn.textContent = p;
                btn.className = (p === currentPage) ? 'active' : '';
                btn.onclick = (() => { const pp = p; return () => { currentPage = pp; renderPage(); }; })();
                pagination.appendChild(btn);
            }

            const next = document.createElement('button');
            next.textContent = 'Next';
            next.disabled = currentPage === pages;
            next.onclick = () => { currentPage++; renderPage(); };
            pagination.appendChild(next);
        }

        function applyFilter(){
            const q = (search.value || '').trim().toLowerCase();
            if (!q){ filtered = rows.slice(); }
            else {
                filtered = rows.filter(r => {
                    return Array.from(r.children).some(td => td.textContent.toLowerCase().indexOf(q) !== -1);
                });
            }
            currentPage = 1;
            renderPage();
        }

        search.addEventListener('input', applyFilter);
        perPageSelect.addEventListener('change', () => { currentPage = 1; renderPage(); });

        // initial render
        renderPage();
    })();
    </script>
</body>
</html>
