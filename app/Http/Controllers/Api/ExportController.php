<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Exports\AgentsExport;
use App\Exports\InvoicesExport;
use App\Exports\PropertiesExport;
use App\Models\Usuario;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Export Users (PDF/Excel)
     */
    public function exportUsers(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->all();
        $date = now()->format('Y-m-d');

        if ($format === 'pdf') {
            $users = (new UsersExport($filters))->query()->get();
            $pdf = Pdf::loadView('pdf.users', [
                'users' => $users,
                'filters' => $filters,
                'date' => now()->format('d/m/Y H:i')
            ]);
            return $pdf->download("usuarios_{$date}.pdf");
        }

        return Excel::download(new UsersExport($filters), "usuarios_{$date}.xlsx");
    }

    /**
     * Export Agents (PDF/Excel)
     */
    public function exportAgentes(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->all();
        $date = now()->format('Y-m-d');

        if ($format === 'pdf') {
            $agents = (new AgentsExport($filters))->query()->get();
            $pdf = Pdf::loadView('pdf.agents', [
                'agents' => $agents,
                'filters' => $filters,
                'date' => now()->format('d/m/Y H:i')
            ]);
            return $pdf->download("agentes_{$date}.pdf");
        }

        return Excel::download(new AgentsExport($filters), "agentes_{$date}.xlsx");
    }

    /**
     * Export Invoices (PDF/Excel)
     */
    public function exportInvoices(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->all();
        $date = now()->format('Y-m-d');

        if ($format === 'pdf') {
            $invoices = (new InvoicesExport($filters))->query()->get();
            $pdf = Pdf::loadView('pdf.invoices', [
                'invoices' => $invoices,
                'filters' => $filters,
                'date' => now()->format('d/m/Y H:i')
            ]);
            return $pdf->download("facturas_{$date}.pdf");
        }

        return Excel::download(new InvoicesExport($filters), "facturas_{$date}.xlsx");
    }

    /**
     * Export Properties (PDF/Excel)
     */
    public function exportPropiedades(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->all();
        $date = now()->format('Y-m-d');

        if ($format === 'pdf') {
            $properties = (new PropertiesExport($filters))->query()->get();
            $pdf = Pdf::loadView('pdf.properties', [
                'properties' => $properties,
                'filters' => $filters,
                'date' => now()->format('d/m/Y H:i')
            ]);
            return $pdf->download("propiedades_{$date}.pdf");
        }

        return Excel::download(new PropertiesExport($filters), "propiedades_{$date}.xlsx");
    }

    /**
     * Export General Summary (PDF/Excel)
     */
    public function exportSummary(Request $request)
    {
        try {
            $format = $request->get('format', 'excel');
            $filters = $request->all();
            $date = now()->format('Y-m-d');

            // 1. Get Data from both internal sources to satisfy the summary view
            $dashboard = new \App\Http\Controllers\DashboardController();
            
            // Get Global Stats (usuarios, agentes, propiedades, pagos)
            $globalStats = $dashboard->getGlobalStats()->getOriginalContent();
            
            // Get Operational Stats (kpis, charts, rankings)
            $operationalStats = $dashboard->getStats($request)->getOriginalContent();
            
            // Merge stats for the view
            $stats = array_merge($globalStats, $operationalStats);

            if ($format === 'pdf') {
                try {
                    $pdf = Pdf::loadView('pdf.summary', [
                        'stats' => $stats,
                        'filters' => $filters,
                        'date' => now()->format('d/m/Y H:i')
                    ]);
                    
                    return $pdf->download("resumen_general_{$date}.pdf");
                } catch (\Exception $pdfException) {
                    \Log::error("DOMPDF ERROR: " . $pdfException->getMessage());
                    
                    // Fallback to basic HTML if Blade fails (User Request #1)
                    $html = "<h1>Error en Generación de PDF</h1><p>Ocurrió un error técnico al procesar la vista.</p>";
                    $html .= "<h2>Resumen de Datos:</h2><ul>";
                    $html .= "<li>Usuarios: " . ($stats['usuarios']['total'] ?? 0) . "</li>";
                    $html .= "<li>Propiedades: " . ($stats['propiedades']['total'] ?? 0) . "</li>";
                    $html .= "<li>Ventas: " . ($stats['pagos']['total'] ?? 0) . "</li>";
                    $html .= "</ul><p>Error técnico: " . $pdfException->getMessage() . "</p>";
                    
                    return Pdf::loadHTML($html)->download("error_debug_{$date}.pdf");
                }
            }

            return Excel::download(new \App\Exports\GeneralSummaryExport($filters), "resumen_general_{$date}.xlsx");

        } catch (\Exception $e) {
            \Log::error("PDF EXPORT ERROR: " . $e->getMessage());
            return response()->json([
                'error' => 'Error al generar el reporte',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
