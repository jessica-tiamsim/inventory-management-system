const inventoryModel = require('../models/inventoryModel');

const dashboardController = {
    getDashboard: async (req, res) => {
        try {
            // Process all three database tasks concurrently
            const [stats, lowStockData, recentActivities] = await Promise.all([
                inventoryModel.getDashboardStats(),
                inventoryModel.getLowStock(),
                inventoryModel.getRecentActivity()
            ]);

            // Clean up numbers and enforce correct math types
            const formattedStats = {
                activeProducts: stats.activeProducts || 0,
                inactiveProducts: stats.inactiveProducts || 0,
                // If inventory has total units, we estimate true inventory costs
                totalUnits: stats.totalUnits < 0 ? 0 : stats.totalUnits,
                inventoryValue: (stats.totalUnits < 0 ? 0 : stats.totalUnits) * (stats.fallbackValue || 0)
            };

            // Hand the organized payload directly to the view engine layout block
            res.render('dashboard', {
                user: req.session.user,
                stats: formattedStats,
                lowStockProducts: lowStockData || [],
                recentActivities: recentActivities || []
            });

        } catch (err) {
            console.error('Critical systems layout exception:', err);
            res.status(500).send('Internal Dashboard Server Error.');
        }
    }
};

module.exports = dashboardController;