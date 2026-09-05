<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = $request->query('token');

        // Simple security check. 
        if ($token !== env('DEPLOY_TOKEN', 'mi_token_secreto_deploy_2026')) {
            abort(403, 'Unauthorized deploy token');
        }

        try {
            Log::info('Deploy iniciado vía Webhook');

            // Set maintenance mode
            Artisan::call('down', ['--render' => 'errors::503', '--secret' => '1630542a-246b-4b66-afa1-dd72a4c43515']);

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            // Clear and build caches
            Artisan::call('optimize:clear');
            $optimizeClearOutput = Artisan::output();
            
            Artisan::call('optimize');
            $optimizeOutput = Artisan::output();

            // Run queue restart so listeners catch new code
            Artisan::call('queue:restart');

            // Bring application up
            Artisan::call('up');

            Log::info('Deploy finalizado exitosamente');

            return response()->json([
                'success' => true,
                'message' => 'Deployment successful!',
                'migrations' => $migrateOutput,
                'optimize_clear' => $optimizeClearOutput,
                'optimize' => $optimizeOutput
            ]);

        } catch (\Exception $e) {
            Log::error('Deploy falló: ' . $e->getMessage());
            Artisan::call('up'); // Ensure it comes back up
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
