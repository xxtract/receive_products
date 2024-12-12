<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Search</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full p-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-2xl font-bold mb-6 text-center">Company Search</h1>
                
                <div class="space-y-4">
                    <div class="relative">
                        <label for="search" class="block text-sm font-medium text-gray-700">GLN</label>
                        <input type="text" id="search" name="search" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Company Name or GLN">
                    </div>
                    
                    <button id="searchBtn" 
                            class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Search
                    </button>
                </div>

                <div id="results" class="mt-6 hidden">
                    <h2 class="text-lg font-semibold mb-3">Results</h2>
                    <div id="resultsContent" class="space-y-3"></div>
                </div>

                <div id="error" class="mt-4 hidden">
                    <p class="text-red-600 text-sm"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const searchBtn = document.getElementById('searchBtn');
            const resultsDiv = document.getElementById('results');
            const resultsContent = document.getElementById('resultsContent');
            const errorDiv = document.getElementById('error');

            function performSearch() {
                const query = searchInput.value.trim();
                
                if (!query) {
                    showError('Please enter a search term');
                    return;
                }

                fetch('/api/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ query })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayResults(data.data);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    showError('An error occurred while searching');
                });
            }

            function displayResults(results) {
                resultsContent.innerHTML = '';
                errorDiv.classList.add('hidden');

                if (results.length === 0) {
                    showError('No results found');
                    return;
                }

                results.forEach(result => {
                    const resultElement = document.createElement('div');
                    resultElement.className = 'p-3 bg-gray-50 rounded';
                    resultElement.innerHTML = `
                        <p class="font-semibold">${result.companyName}</p>
                        <p class="text-sm text-gray-600">Information Provider: ${result.informationProvider}</p>
                    `;
                    resultsContent.appendChild(resultElement);
                });

                resultsDiv.classList.remove('hidden');
            }

            function showError(message) {
                errorDiv.querySelector('p').textContent = message;
                errorDiv.classList.remove('hidden');
                resultsDiv.classList.add('hidden');
            }

            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
</body>
</html>
