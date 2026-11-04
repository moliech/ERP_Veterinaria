<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    @php
        $corp = $company ?? \App\Models\CompanySetting::current();
        $accentHex = $corp->primaryColorHex();
    @endphp
<style>
    @page {
        margin: 120px 40px 70px 40px;
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1e293b;
        line-height: 1.4;
    }

    header {
        position: fixed;
        top: -95px;
        left: 0;
        right: 0;
        height: 85px;
        border-bottom: 2px solid {{ $accentHex }};
    }

    footer {
        position: fixed;
        bottom: -50px;
        left: 0;
        right: 0;
        height: 35px;
        font-size: 9px;
        color: #64748b;
        text-align: center;
        border-top: 1px solid #cbd5e1;
        padding-top: 6px;
    }

    .brand {
        font-size: 18px;
        font-weight: bold;
        color: {{ $accentHex }};
    }

    .doc-box {
        border: 2px solid {{ $accentHex }};
        border-radius: 6px;
        padding: 6px 12px;
        text-align: center;
        background-color: #f8fafc;
    }

    .doc-type {
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        color: {{ $accentHex }};
    }

    .doc-number {
        font-size: 16px;
        font-weight: bold;
        color: #0f172a;
    }

    table.data {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    table.data th {
        background-color: #0f172a;
        color: #ffffff;
        padding: 6px 8px;
        font-size: 10px;
        text-transform: uppercase;
        text-align: left;
    }

    table.data td {
        padding: 6px 8px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 10px;
    }

    table.totals {
        width: 40%;
        margin-left: auto;
        margin-top: 15px;
        border-collapse: collapse;
    }

    table.totals td {
        padding: 4px 8px;
        font-size: 10px;
    }

    .total-row {
        font-size: 13px;
        font-weight: bold;
        color: {{ $accentHex }};
        border-top: 2px solid {{ $accentHex }};
    }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: bold;
    }
</style>
</head>

<body>
    <header>
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <div class="brand">
                        {{ $corp->company_name }}
                    </div>

                    <div style="font-size: 10px; color: #475569;">
                        {{ $corp->business_subtitle }}
                    </div>

                    <div style="font-size: 9px; color: #64748b;">
                        NIT: {{ $corp->nit }} · {{ $corp->address }} · Tel: {{ $corp->phone }}
                    </div>
                </td>

                <td style="width: 40%; text-align: right;">
                    <div class="doc-box">
                        <div class="doc-type">@yield('doc_type')</div>
                        <div class="doc-number">@yield('doc_number')</div>
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        {{ $corp->invoice_footer ?: 'Documento oficial generado por ' . $corp->company_name }} · Emitido el {{ now()->format('d/m/Y H:i') }}
    </footer>

    <main>
        @yield('content')
    </main>
</body>
</html>
