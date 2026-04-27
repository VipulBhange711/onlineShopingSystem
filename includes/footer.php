    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-1">
                    <a href="<?php echo SITE_URL; ?>index.php" class="text-2xl font-bold text-primary">Shop<span class="text-gray-900 dark:text-white">Ease</span></a>
                    <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                        Your one-stop destination for the latest in electronics, fashion, and more. Experience seamless shopping with ShopEase.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-primary transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Shop</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="<?php echo SITE_URL; ?>products.php" class="text-gray-600 dark:text-gray-400 hover:text-primary transition text-sm">All Products</a></li>
                        <li><a href="<?php echo SITE_URL; ?>products.php?category=1" class="text-gray-600 dark:text-gray-400 hover:text-primary transition text-sm">Electronics</a></li>
                        <li><a href="<?php echo SITE_URL; ?>products.php?category=2" class="text-gray-600 dark:text-gray-400 hover:text-primary transition text-sm">Fashion</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Company</h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary transition text-sm">About Us</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary transition text-sm">Contact</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary transition text-sm">Terms of Service</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Newsletter</h3>
                    <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm">Subscribe to get the latest updates and offers.</p>
                    <form class="mt-4 flex">
                        <input type="email" placeholder="Email address" class="w-full px-4 py-2 rounded-l-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary">
                        <button class="bg-primary text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">Join</button>
                    </form>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 dark:text-gray-400 text-xs text-center md:text-left">
                    &copy; 2026 ShopEase Online Shopping Website. All rights reserved. Built for MCA Students.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" class="h-4 opacity-50 grayscale hover:grayscale-0 transition">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" class="h-4 opacity-50 grayscale hover:grayscale-0 transition">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-4 opacity-50 grayscale hover:grayscale-0 transition">
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
    <script>
        // Provide SITE_URL to JS
        const SITE_URL = '<?php echo SITE_URL; ?>';
        
        // Dark Mode Toggle Logic
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        darkModeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        });
    </script>
</body>
</html>
