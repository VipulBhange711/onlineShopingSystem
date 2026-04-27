        </div>
    </main>

    <script>
        // Set active link
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (currentPath.includes(link.getAttribute('href'))) {
                link.classList.add('active');
                document.getElementById('page-title').innerText = link.querySelector('span').innerText;
            }
        });
    </script>
</body>
</html>
