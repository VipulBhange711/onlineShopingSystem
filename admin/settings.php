<?php
include 'includes/header.php';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setFlashMessage('success', 'Settings updated successfully!');
    redirect('settings.php');
}

$settings = [
    'site_name' => 'ShopZone',
    'site_email' => 'support@shopzone.com',
    'currency' => 'USD',
    'tax_rate' => '5',
    'shipping_cost' => '10'
];
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-black text-gray-800">System Settings</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">General Settings</h3>
        <form action="settings.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Site Name</label>
                <input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Support Email</label>
                <input type="email" name="site_email" value="<?php echo $settings['site_email']; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Currency</label>
                <select name="currency" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="USD" <?php echo $settings['currency'] === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                    <option value="EUR" <?php echo $settings['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                    <option value="GBP" <?php echo $settings['currency'] === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">Save Changes</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Store Settings</h3>
        <form action="settings.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tax Rate (%)</label>
                <input type="number" name="tax_rate" value="<?php echo $settings['tax_rate']; ?>" step="0.1" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Shipping Cost ($)</label>
                <input type="number" name="shipping_cost" value="<?php echo $settings['shipping_cost']; ?>" step="0.01" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-500/30">Save Settings</button>
        </form>
    </div>
</div>

<div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">System Information</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="text-center p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">PHP Version</p>
            <p class="font-bold text-gray-900"><?php echo phpversion(); ?></p>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Database</p>
            <p class="font-bold text-gray-900">MySQL</p>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Server</p>
            <p class="font-bold text-gray-900">Apache/Nginx</p>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Project Version</p>
            <p class="font-bold text-gray-900">1.0.0</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
