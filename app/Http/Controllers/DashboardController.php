<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Propiedad;
use App\Models\Pago;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getGlobalStats()
    {
        // 1. Usuarios (Clientes/Usuarios base)
        $usuariosTotal = Usuario::clientes()->count() ?: 0;
        $usuariosActivos = Usuario::clientes()->where('activo', 1)->count() ?: 0;
        $usuariosBloqueados = $usuariosTotal - $usuariosActivos;
        
        $usuariosPorRol = Usuario::join('roles', 'usuarios.idRol', '=', 'roles.idRol')
            ->select('roles.nombre as label', DB::raw('count(*) as value'))
            ->groupBy('roles.nombre', 'roles.idRol')
            ->get() ?: collect([]);

        // 2. Agentes (Usuarios con idRol 3)
        $agentesTotal = Usuario::agentes()->count() ?: 0;
        $agentesActivos = Usuario::agentes()->where('activo', 1)->count() ?: 0;
        $agentesInactivos = $agentesTotal - $agentesActivos;

        // 3. Propiedades
        $propiedadesTotal = Propiedad::count() ?: 0;
        $propiedadesDisponibles = Propiedad::where('estado', 'disponible')->count() ?: 0;
        $propiedadesVendidas = Propiedad::where('estado', 'vendida')->count() ?: 0;
        $propiedadesArrendadas = Propiedad::where('estado', 'arrendada')->count() ?: 0;
        $propiedadesReservadas = Propiedad::where('estado', 'reservada')->count() ?: 0;
        $valorInventarioDisponible = Propiedad::where('estado', 'disponible')->sum('precio') ?: 0;

        // 4. Pagos
        $pagosTotal = Pago::count() ?: 0;
        $pagosAprobados = Pago::where('estado', 'aprobado')->count() ?: 0;
        $pagosPendientes = Pago::where('estado', 'pendiente')->count() ?: 0;
        $pagosRechazados = Pago::where('estado', 'rechazado')->count() ?: 0;
        $montoAprobado = Pago::where('estado', 'aprobado')->sum('monto') ?: 0;
        $montoPendiente = Pago::where('estado', 'pendiente')->sum('monto') ?: 0;

        return response()->json([
            'usuarios' => [
                'total' => (int)$usuariosTotal,
                'activos' => (int)$usuariosActivos,
                'bloqueados' => (int)$usuariosBloqueados,
                'porRol' => $usuariosPorRol
            ],
            'agentes' => [
                'total' => (int)$agentesTotal,
                'activos' => (int)$agentesActivos,
                'inactivos' => (int)$agentesInactivos
            ],
            'propiedades' => [
                'total' => (int)$propiedadesTotal,
                'disponibles' => (int)$propiedadesDisponibles,
                'vendidas' => (int)$propiedadesVendidas,
                'arrendadas' => (int)$propiedadesArrendadas,
                'reservadas' => (int)$propiedadesReservadas,
                'valorInventario' => (float)$valorInventarioDisponible
            ],
            'pagos' => [
                'total' => (int)$pagosTotal,
                'aprobados' => (int)$pagosAprobados,
                'pendientes' => (int)$pagosPendientes,
                'rechazados' => (int)$pagosRechazados,
                'montoAprobado' => (float)$montoAprobado,
                'montoPendiente' => (float)$montoPendiente
            ]
        ]);
    }

    public function getStats(Request $request)
    {
        $period = $request->query('period', 'year');
        [$start, $end] = $this->getPeriodRange($period);

        // KPIS Filtrados por periodo
        $income = Pago::where('estado', 'aprobado')->whereBetween('fecha', [$start, $end])->sum('monto') ?: 0;
        $salesCount = Pago::where('estado', 'aprobado')->whereBetween('fecha', [$start, $end])->count() ?: 0;
        $newUsers = Usuario::whereBetween('creado_en', [$start, $end])->count() ?: 0;
        $newProperties = Propiedad::whereBetween('creado_en', [$start, $end])->count() ?: 0;
        $activeAgents = Usuario::agentes()->where('activo', 1)->count() ?: 0;

        // CHARTS
        $usuariosPorRol = Usuario::join('roles', 'usuarios.idRol', '=', 'roles.idRol')
            ->select('roles.nombre as label', DB::raw('count(*) as value'))
            ->groupBy('roles.nombre', 'roles.idRol')
            ->get() ?: collect([]);

        $ventasPorEstado = Pago::select('estado as label', DB::raw('count(*) as value'))
            ->whereBetween('fecha', [$start, $end])
            ->groupBy('estado')
            ->get() ?: collect([]);

        $propiedadesPorEstado = Propiedad::select('estado as label', DB::raw('count(*) as value'))
            ->groupBy('estado')
            ->get() ?: collect([]);

        $ventasPorTiempo = $this->getChartData($period, $start, $end) ?: [];

        return response()->json([
            'kpis' => [
                'income' => [
                    'current' => (float)$income,
                    'change' => 0 
                ],
                'sales' => [
                    'current' => (int)$salesCount,
                    'change' => 0
                ],
                'users' => [
                    'current' => (int)$newUsers,
                    'change' => 0
                ],
                'properties' => [
                    'current' => (int)$newProperties,
                    'change' => 0
                ],
                'activeAgents' => (int)$activeAgents
            ],
            'charts' => [
                'usuariosPorRol' => $usuariosPorRol,
                'ventasPorEstado' => $ventasPorEstado,
                'propiedadesPorEstado' => $propiedadesPorEstado,
                'ventasPorTiempo' => $ventasPorTiempo
            ],
            'rankings' => [
                'topAgente' => null, 
                'topPropiedad' => null
            ]
        ]);
    }

    public function getUserStats(Request $request)
    {
        $genderDistribution = Usuario::select('genero as name', DB::raw('count(*) as value'))
            ->groupBy('genero')
            ->get() ?: collect([]);

        $totalUsers = Usuario::count() ?: 0;
        $enteredOnce = Usuario::where('login_count', '>=', 1)->count() ?: 0;
        $enteredMore = Usuario::where('login_count', '>=', 5)->count() ?: 0;
        $heavyUsers = Usuario::where('login_count', '>=', 20)->count() ?: 0;
        
        $buyers = Pago::where('estado', 'aprobado')
            ->distinct('docUsuario')
            ->count('docUsuario') ?: 0;

        $funnelData = [
            ['name' => 'Entraron 1 vez', 'value' => (int)$enteredOnce, 'color' => '#6b0000'],
            ['name' => 'Entraron 5+ veces', 'value' => (int)$enteredMore, 'color' => '#d40000'],
            ['name' => 'Entraron 20+ veces', 'value' => (int)$heavyUsers, 'color' => '#ffcc00'],
            ['name' => 'Compradores', 'value' => (int)$buyers, 'color' => '#808080'],
        ];

        $recentUsers = Usuario::orderBy('creado_en', 'desc')
            ->limit(5)
            ->get(['nombre', 'correo', 'creado_en']) ?: collect([]);

        return response()->json([
            'genderDistribution' => $genderDistribution,
            'funnelData' => $funnelData,
            'recentUsers' => $recentUsers,
            'totalUsers' => (int)$totalUsers
        ]);
    }

    private function getPeriodRange($period)
    {
        $now = Carbon::now();
        switch ($period) {
            case 'day':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'year':
            default:
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
        }
        return [$start, $end];
    }

    private function getChartData($period, $start, $end)
    {
        $query = Pago::where('estado', 'aprobado')
            ->whereBetween('fecha', [$start, $end]);

        if ($period == 'day') {
            $data = $query->select(
                DB::raw('HOUR(fecha) as label'),
                DB::raw('SUM(monto) as value')
            )->groupBy('label')->orderBy('label')->get();
            return $data->map(fn($item) => ['label' => sprintf("%02d:00", $item->label), 'value' => (float)$item->value])->toArray();
        }

        if ($period == 'year') {
            $data = $query->select(
                DB::raw('MONTH(fecha) as label'),
                DB::raw('SUM(monto) as value')
            )->groupBy('label')->orderBy('label')->get();
            $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            return $data->map(fn($item) => ['label' => $months[$item->label - 1] ?? '?', 'value' => (float)$item->value])->toArray();
        }

        $data = $query->select(
            DB::raw('DATE(fecha) as label'),
            DB::raw('SUM(monto) as value')
        )->groupBy('label')->orderBy('label')->get();
        
        return $data->map(fn($item) => ['label' => Carbon::parse($item->label)->format('d/m'), 'value' => (float)$item->value])->toArray();
    }
}
