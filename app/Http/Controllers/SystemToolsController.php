<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemToolsController extends Controller
{
    public function index()
    {
        return view('system-tools.index');
    }

    private function runCommands(array $commands)
    {
        $log = [];

        foreach ($commands as $cmd) {
            Artisan::call($cmd);
            $log[] = ">> {$cmd}\n" . trim(Artisan::output());
        }

        return implode("\n\n", $log);
    }

    public function clearCache()
    {
        $output = $this->runCommands([
            'cache:clear',
            'config:clear',
            'route:clear',
            'view:clear',
        ]);

        return back()->with('system_tools_output', $output);
    }

    public function buildCache()
    {
        $output = $this->runCommands([
            'config:cache',
            'route:cache',
            'view:cache',
        ]);

        return back()->with('system_tools_output', $output);
    }

    public function optimize()
    {
        $output = $this->runCommands([
            'optimize',
        ]);

        return back()->with('system_tools_output', $output);
    }

    public function optimizeClear()
    {
        $output = $this->runCommands([
            'optimize:clear',
        ]);

        return back()->with('system_tools_output', $output);
    }

    public function storageLink()
    {
        $output = $this->runCommands([
            'storage:link',
        ]);

        return back()->with('system_tools_output', $output);
    }

    public function queueRestart()
    {
        $output = $this->runCommands([
            'queue:restart',
        ]);

        return back()->with('system_tools_output', $output);
    }
}
