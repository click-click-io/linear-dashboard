<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linear Issues Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .dark-mode {
            background-color: #1a202c; /* Darker background for better contrast */
            color: #cbd5e0; /* Softer text color */
        }
        .dark-mode .tile {
            background-color: #2d3748; /* Darker tile background */
            color: #e2e8f0; /* Light text color */
        }
        .dark-mode table {
            background-color: #2d3748;
            color: #e2e8f0;
        }
        .dark-mode th {
            background-color: #4a5568;
            color: #edf2f7;
        }
        .dark-mode td {
            border-color: #4a5568;
            background-color: #2d3748; /* Grey background */
            color: #e2e8f0;
        }
        .dark-mode tr:hover {
            background-color: #4a5568;
        }
        .dark-mode .filter-active {
            background-color: #5a67d8;
            color: #ffffff;
        }
        .dark-mode .filter-inactive {
            color: #a0aec0;
        }
        .tile {
            backdrop-filter: blur(10px);
        }
        .filter-active {
            background-color: #4f46e5;
            color: white;
        }
        .filter-inactive {
            background-color: transparent;
            color: #6b7280;
        }
        .dark .filter-inactive {
            color: #9ca3af;
        }
        .dark-mode .dashboard-container {
            background-color: #1a202c;
        }
        .dark-mode .header .title {
            color: #f7fafc;
        }
        .dark-mode .issues-table-container {
            background-color: #2d3748;
        }
        .dark-mode .metric-tile {
            background-color: #4a5568;
            color: #e2e8f0;
        }
        .dark-mode th {
            background-color: #2d3748;
            color: #e2e8f0;
        }
        .sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 400px;
            height: 100%;
            background-color: #2d3748;
            color: #e2e8f0;
            box-shadow: -2px 0 5px rgba(0,0,0,0.5);
            transition: right 0.3s ease;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar.open {
            right: 0;
        }
    </style>
</head>
<body class="bg-gray-50" id="body">
    <div class="dashboard-container min-h-screen">
        <div class="content-wrapper max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="header flex justify-between items-center mb-4 px-6">
                <h1 class="title text-3xl font-bold text-gray-900 dark:text-white">Click-Click.io Management Dashboard</h1>
                <button id="toggle-dark-mode" 
                    class="dark-mode-toggle px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 
                    transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-4.34l-.707-.707M4.34 4.34l-.707-.707M21 12h-1M4 12H3m16.66 4.34l-.707.707M4.34 19.66l-.707.707M16 12a4 4 0 01-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
            </div>

            <div class="main-content container mx-auto px-4 py-8">
                <!-- Debug Button -->
                <button id="debug-button" 
                    class="debug-button fixed bottom-4 right-4 bg-gray-800 dark:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11h-6m-2 0H5m8 0a4 4 0 11-8 0 4 4 0 018 0zm0 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <!-- Debug Modal -->
                <div id="debug-modal" class="debug-modal hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="modal-content bg-white dark:bg-gray-800 rounded-xl shadow-xl w-3/4 h-3/4 max-w-4xl overflow-hidden flex flex-col">
                        <div class="modal-header px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="modal-title text-lg font-medium text-gray-900 dark:text-gray-100">GraphQL Debug Output</h3>
                            <div class="modal-actions flex items-center space-x-4">
                                <button id="copy-debug-data" class="copy-button text-sm px-3 py-1 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition-colors duration-200 flex items-center space-x-2">
                                    <span id="copy-text">Copy to Clipboard</span>
                                    <svg class="icon h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                                <button id="close-debug-modal" class="close-button text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                    <svg class="icon h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body flex-1 p-6 overflow-auto">
                            <pre id="debug-content" class="debug-content bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-auto text-sm text-gray-800 dark:text-gray-200"></pre>
                        </div>
                    </div>
                </div>

                <div class="metrics-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="metric-tile bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <h2 class="metric-title text-lg font-semibold opacity-90">Total Open Tickets</h2>
                        <p id="total-open" class="metric-value text-4xl font-bold mt-2">0</p>
                    </div>
                    <div class="metric-tile bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <h2 class="metric-title text-lg font-semibold opacity-90">In Progress Tickets</h2>
                        <p id="in-progress" class="metric-value text-4xl font-bold mt-2">0</p>
                    </div>
                    <div class="metric-tile bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <h2 class="metric-title text-lg font-semibold opacity-90">In Review Tickets</h2>
                        <p id="in-review" class="metric-value text-4xl font-bold mt-2">0</p>
                    </div>
                    <div class="metric-tile bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <h2 class="metric-title text-lg font-semibold opacity-90">Due Tickets</h2>
                        <p id="due-tickets" class="metric-value text-4xl font-bold mt-2">0</p>
                    </div>
                </div>

                <div class="issues-table-container bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="table-controls px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="filter-buttons flex items-center space-x-4">
                            <button id="show-this-week" 
                                class="filter-button px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 filter-active">
                                Due This Week
                            </button>
                            <button id="show-all-tickets" 
                                class="filter-button px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 filter-inactive">
                                All Tickets
                            </button>
                        </div>
                    </div>
                    <table class="issues-table min-w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="table-header bg-gray-50 dark:bg-gray-700">
                                <th class="header-cell w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Identifier</th>
                                <th class="header-cell w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assignee</th>
                                <th class="header-cell w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="header-cell w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State</th>
                                <th class="header-cell w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                                <th class="header-cell w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                            </tr>
                        </thead>
                        <tbody id="issues-table-body" class="table-body bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Issues will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="ticket-sidebar" class="sidebar">
            <div class="p-4">
                <h2 id="ticket-title" class="text-xl font-bold mb-2">Ticket Title</h2>
                <p id="ticket-identifier" class="text-sm text-gray-400 mb-4">Identifier</p>
                <div id="ticket-details">
                    <!-- Ticket details will be populated here -->
                </div>
                <button onclick="closeSidebar()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded">Close</button>
            </div>
        </div>
    </div>

    <script>
        let currentFilter = 'week';
        let issuesData = [];
        let debugData = null;

        // Debug Modal Functions
        const setupDebugModal = () => {
            const modal = document.getElementById('debug-modal');
            const debugButton = document.getElementById('debug-button');
            const closeButton = document.getElementById('close-debug-modal');
            const debugContent = document.getElementById('debug-content');
            const copyButton = document.getElementById('copy-debug-data');
            const copyText = document.getElementById('copy-text');

            if (!modal || !debugButton || !closeButton || !debugContent || !copyButton || !copyText) {
                console.error('Debug modal elements not found');
                return;
            }

            debugButton.onclick = () => {
                modal.classList.remove('hidden');
                debugContent.textContent = debugData ? JSON.stringify(debugData, null, 2) : 'No data available yet';
            };

            closeButton.onclick = () => {
                modal.classList.add('hidden');
            };

            copyButton.onclick = async () => {
                try {
                    const textToCopy = debugData ? JSON.stringify(debugData, null, 2) : 'No data available';
                    await navigator.clipboard.writeText(textToCopy);
                    
                    // Visual feedback
                    copyText.textContent = 'Copied!';
                    copyButton.classList.add('bg-green-500');
                    copyButton.classList.remove('bg-indigo-500', 'hover:bg-indigo-600');
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        copyText.textContent = 'Copy to Clipboard';
                        copyButton.classList.remove('bg-green-500');
                        copyButton.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy text: ', err);
                    copyText.textContent = 'Failed to copy';
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        copyText.textContent = 'Copy to Clipboard';
                    }, 2000);
                }
            };

            // Close modal when clicking outside
            window.onclick = (event) => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            };
        };

        function isThisWeek(date) {
            const now = new Date();
            const weekStart = new Date(now.getFullYear(), now.getMonth(), now.getDate() - now.getDay());
            const weekEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate() - now.getDay() + 6);
            date = new Date(date);
            return date >= weekStart && date <= weekEnd;
        }

        function getInitials(name) {
            return name
                .split(' ')
                .map(word => word[0])
                .join('')
                .toUpperCase();
        }

        function updateButtonStyles() {
            const allButton = document.getElementById('show-all-tickets');
            const weekButton = document.getElementById('show-this-week');
            
            if (currentFilter === 'all') {
                allButton.classList.add('filter-active');
                allButton.classList.remove('filter-inactive');
                weekButton.classList.add('filter-inactive');
                weekButton.classList.remove('filter-active');
            } else {
                weekButton.classList.add('filter-active');
                weekButton.classList.remove('filter-inactive');
                allButton.classList.add('filter-inactive');
                allButton.classList.remove('filter-active');
            }
        }

        function getProjectColor(projectName) {
            if (!projectName) return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            
            // Generate a consistent color based on the project name
            const colors = {
                'Frontend': 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-200',
                'Backend': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-200',
                'Infrastructure': 'bg-purple-100 text-purple-800 dark:bg-purple-700 dark:text-purple-200',
                'Mobile': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200',
                'Design': 'bg-pink-100 text-pink-800 dark:bg-pink-700 dark:text-pink-200'
            };

            // Hash the project name to get a consistent index
            const hash = projectName.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
            const colorKeys = Object.keys(colors);
            const colorKey = colorKeys[hash % colorKeys.length];
            
            return colors[colorKey];
        }

        function filterAndDisplayIssues() {
            const filteredIssues = currentFilter === 'all' 
                ? issuesData.filter(issue => issue.state.name !== 'Done')
                : issuesData.filter(issue => issue.dueDate && isThisWeek(issue.dueDate) && issue.state.name !== 'Done');

            const issuesTableBody = document.getElementById('issues-table-body');
            issuesTableBody.innerHTML = '';
            
            const groupedIssues = filteredIssues.reduce((groups, issue) => {
                const teamName = issue.team ? issue.team.name : 'No Team';
                if (!groups[teamName]) {
                    groups[teamName] = [];
                }
                groups[teamName].push(issue);
                return groups;
            }, {});

            Object.keys(groupedIssues).forEach(teamName => {
                const teamHeaderRow = document.createElement('tr');
                teamHeaderRow.className = 'bg-gray-100 dark:bg-gray-900';
                teamHeaderRow.innerHTML = `
                    <td colspan="6" class="px-6 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                        ${teamName}
                    </td>
                `;
                issuesTableBody.appendChild(teamHeaderRow);

                groupedIssues[teamName].forEach(issue => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer';
                    row.onclick = () => {
                        openSidebar(issue.identifier);
                    };

                    const dueDate = new Date(issue.dueDate);
                    const today = new Date();
                    const isDueToday = issue.dueDate && dueDate.toDateString() === today.toDateString();
                    const isOverdue = !issue.dueDate || (issue.dueDate && dueDate < today);
                    const isFuture = issue.dueDate && dueDate > today;
                    const dueDateClass = isDueToday 
                        ? 'bg-yellow-500 text-white px-2 py-1 rounded-full' 
                        : isOverdue 
                        ? 'bg-red-500 text-white px-2 py-1 rounded-full' 
                        : isFuture 
                        ? 'bg-green-500 text-white px-2 py-1 rounded-full' 
                        : '';

                    const assigneeContent = issue.assignee 
                        ? issue.assignee.avatarUrl 
                            ? `<img class="h-8 w-8 rounded-full object-cover" 
                                    src="${issue.assignee.avatarUrl}" 
                                    alt="${issue.assignee.name}"
                                    title="${issue.assignee.name}"
                                    onerror="this.onerror=null; this.innerHTML='<div class=\'h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm font-medium text-gray-500 dark:text-gray-400\'>${getInitials(issue.assignee.name)}</div>';">`
                            : `<div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                    ${getInitials(issue.assignee.name)}
                               </div>`
                        : `<div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                           </div>`;

                    row.innerHTML = `
                        <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${issue.identifier}
                        </td>
                        <td class="w-1/6 px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                ${assigneeContent}
                            </div>
                        </td>
                        <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            ${issue.title}
                        </td>
                        <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                ${getStateColor(issue.state.name)}">
                                ${issue.state.name}
                            </span>
                        </td>
                        <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                ${issue.project ? getProjectColor(issue.project.name) : getProjectColor(null)}">
                                ${issue.project ? issue.project.name : 'No Project'}
                            </span>
                        </td>
                        <td class="w-1/6 px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" style="padding-right: 10px;">
                            <span class="${dueDateClass}">${issue.dueDate ? dueDate.toLocaleDateString() : 'No due date'}</span>
                        </td>
                    `;
                    issuesTableBody.appendChild(row);
                });
            });
        }

        function getStateColor(state) {
            switch (state) {
                case 'In Progress':
                    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200';
                case 'In Review':
                    return 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-200';
                case 'Todo':
                    return 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-200';
                default:
                    return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            }
        }

        async function fetchMetrics() {
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('linear-token');
            if (!token) {
                console.error('No token provided');
                return;
            }

            try {
                const response = await fetch(`/api/metrics?linear-token=${token}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                debugData = data; // Store the data for debug modal
                console.log('Full API Response:', data);

                if (data.issues && data.issues.length > 0) {
                    issuesData = data.issues;
                    const totalOpen = data.issues.filter(issue => ['Todo', 'In Progress', 'In Review'].includes(issue.state.name)).length;
                    const inProgress = data.issues.filter(issue => issue.state.name === 'In Progress').length;
                    const inReview = data.issues.filter(issue => issue.state.name === 'In Review').length;
                    const dueTickets = data.issues.filter(issue => ['Todo', 'In Progress'].includes(issue.state.name) && issue.dueDate && new Date(issue.dueDate) < new Date()).length;

                    document.getElementById('total-open').innerText = totalOpen;
                    document.getElementById('in-progress').innerText = inProgress;
                    document.getElementById('in-review').innerText = inReview;
                    document.getElementById('due-tickets').innerText = dueTickets;

                    updateButtonStyles();
                    filterAndDisplayIssues();
                }
            } catch (error) {
                console.error('Error fetching metrics:', error.message);
                debugData = { error: error.message };
            }
        }

        function openSidebar(identifier) {
            const sidebar = document.getElementById('ticket-sidebar');
            // Fetch ticket details using identifier
            fetch(`/task/${identifier}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ticket-title').innerText = data.title;
                    document.getElementById('ticket-identifier').innerText = data.identifier;
                    document.getElementById('ticket-details').innerHTML = `
                        <p><strong>State:</strong> ${data.state.name}</p>
                        <p><strong>Project:</strong> ${data.project ? data.project.name : 'None'}</p>
                        <p><strong>Assignee:</strong> ${data.assignee ? data.assignee.name : 'Unassigned'}</p>
                        <p><strong>Description:</strong> ${data.description || 'No description'}</p>
                    `;
                });
            sidebar.classList.add('open');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('ticket-sidebar');
            sidebar.classList.remove('open');
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Setting up debug modal...');
            setupDebugModal();
            
            // Set up filter button listeners
            document.getElementById('show-all-tickets').addEventListener('click', () => {
                currentFilter = 'all';
                updateButtonStyles();
                filterAndDisplayIssues();
            });

            document.getElementById('show-this-week').addEventListener('click', () => {
                currentFilter = 'week';
                updateButtonStyles();
                filterAndDisplayIssues();
            });

            // Set up dark mode toggle
            document.getElementById('toggle-dark-mode').addEventListener('click', () => {
                const body = document.getElementById('body');
                body.classList.toggle('dark-mode');
            });

            // Initial data fetch
            fetchMetrics();
        });
    </script>
</body>
</html>
