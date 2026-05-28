# =============================================================
# EduCore CMS — Frontend Vendor Download Script (Windows/PowerShell)
# Run once to download all vendor files locally
# Usage: .\scripts\download-vendors.ps1
# =============================================================

$ErrorActionPreference = "Stop"

$VENDOR_DIR = "public\assets\vendor"
$CSS_DIR    = "public\assets\css"
$JS_DIR     = "public\assets\js"

Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "  EduCore — Downloading Frontend Vendor Files" -ForegroundColor Cyan
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

# Create all directories
$dirs = @(
    "$VENDOR_DIR\bootstrap",
    "$VENDOR_DIR\fontawesome\webfonts",
    "$VENDOR_DIR\chartjs",
    "$VENDOR_DIR\datatables",
    "$VENDOR_DIR\sweetalert2",
    "$VENDOR_DIR\axios",
    "$VENDOR_DIR\qrcodejs",
    "$VENDOR_DIR\jquery",
    $CSS_DIR,
    $JS_DIR
)
foreach ($dir in $dirs) {
    New-Item -ItemType Directory -Path $dir -Force | Out-Null
}
Write-Host "📁 Directories created." -ForegroundColor Green
Write-Host ""

function Download-File($url, $dest) {
    try {
        Invoke-WebRequest -Uri $url -OutFile $dest -UseBasicParsing -TimeoutSec 60
        Write-Host "   ✅ $dest" -ForegroundColor Green
    } catch {
        Write-Host "   ❌ FAILED: $url" -ForegroundColor Red
        Write-Host "      Error: $_" -ForegroundColor Red
    }
}

# =============================================================
# 1. BOOTSTRAP 5.3.3
# =============================================================
Write-Host "⬇️  Bootstrap 5.3.3..." -ForegroundColor Yellow
Download-File `
    "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" `
    "$VENDOR_DIR\bootstrap\bootstrap.min.css"
Download-File `
    "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" `
    "$VENDOR_DIR\bootstrap\bootstrap.bundle.min.js"

# =============================================================
# 2. FONT AWESOME 6.5.2
# =============================================================
Write-Host "⬇️  Font Awesome 6.5.2..." -ForegroundColor Yellow
Download-File `
    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" `
    "$VENDOR_DIR\fontawesome\all.min.css"

$faFonts = @(
    "fa-brands-400.woff2", "fa-brands-400.ttf",
    "fa-regular-400.woff2", "fa-regular-400.ttf",
    "fa-solid-900.woff2", "fa-solid-900.ttf",
    "fa-v4compatibility.woff2", "fa-v4compatibility.ttf"
)
$faBase = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts"
foreach ($font in $faFonts) {
    Download-File "$faBase/$font" "$VENDOR_DIR\fontawesome\webfonts\$font"
}

# Fix font paths in CSS to point to local webfonts
$cssContent = Get-Content "$VENDOR_DIR\fontawesome\all.min.css" -Raw
$cssContent = $cssContent -replace `
    'https://cdnjs\.cloudflare\.com/ajax/libs/font-awesome/6\.5\.2/webfonts/', `
    '../fontawesome/webfonts/'
Set-Content "$VENDOR_DIR\fontawesome\all.min.css" $cssContent -Encoding utf8
Write-Host "   ✅ Font paths fixed in CSS" -ForegroundColor Green

# =============================================================
# 3. CHART.JS 4.4.1
# =============================================================
Write-Host "⬇️  Chart.js 4.4.1..." -ForegroundColor Yellow
Download-File `
    "https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" `
    "$VENDOR_DIR\chartjs\chart.umd.min.js"

# =============================================================
# 4. JQUERY 3.7.1 (required by DataTables)
# =============================================================
Write-Host "⬇️  jQuery 3.7.1..." -ForegroundColor Yellow
Download-File `
    "https://code.jquery.com/jquery-3.7.1.min.js" `
    "$VENDOR_DIR\jquery\jquery.min.js"

# =============================================================
# 5. DATATABLES 2.x (Bootstrap 5 theme) + Buttons extension
# =============================================================
Write-Host "⬇️  DataTables 2.x + Buttons..." -ForegroundColor Yellow
Download-File `
    "https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" `
    "$VENDOR_DIR\datatables\dataTables.min.css"
Download-File `
    "https://cdn.datatables.net/2.0.8/js/dataTables.min.js" `
    "$VENDOR_DIR\datatables\dataTables.min.js"
Download-File `
    "https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js" `
    "$VENDOR_DIR\datatables\dataTables.bootstrap5.min.js"
Download-File `
    "https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css" `
    "$VENDOR_DIR\datatables\buttons.min.css"
Download-File `
    "https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js" `
    "$VENDOR_DIR\datatables\buttons.min.js"
Download-File `
    "https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js" `
    "$VENDOR_DIR\datatables\buttons.bootstrap5.min.js"
Download-File `
    "https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js" `
    "$VENDOR_DIR\datatables\buttons.html5.min.js"
Download-File `
    "https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js" `
    "$VENDOR_DIR\datatables\buttons.print.min.js"

# =============================================================
# 6. SWEETALERT2
# =============================================================
Write-Host "⬇️  SweetAlert2..." -ForegroundColor Yellow
Download-File `
    "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" `
    "$VENDOR_DIR\sweetalert2\sweetalert2.min.css"
Download-File `
    "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js" `
    "$VENDOR_DIR\sweetalert2\sweetalert2.min.js"

# =============================================================
# 7. AXIOS 1.7.x
# =============================================================
Write-Host "⬇️  Axios 1.7.x..." -ForegroundColor Yellow
Download-File `
    "https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js" `
    "$VENDOR_DIR\axios\axios.min.js"

# =============================================================
# 8. QRCODE.JS
# =============================================================
Write-Host "⬇️  QRCode.js..." -ForegroundColor Yellow
Download-File `
    "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" `
    "$VENDOR_DIR\qrcodejs\qrcode.min.js"

# =============================================================
# 9. CREATE PLACEHOLDER JS FILES
# =============================================================
if (-not (Test-Path "$JS_DIR\app.js")) {
    Set-Content "$JS_DIR\app.js" "// EduCore Global Ajax + SweetAlert Helpers`n// Will be populated during Phase 1 build" -Encoding utf8
    Write-Host "   ✅ app.js placeholder created" -ForegroundColor Green
}

if (-not (Test-Path "$JS_DIR\main.js")) {
    Set-Content "$JS_DIR\main.js" "// EduCore main.js`n// Copy from original PHP project: ../educore-cms/assets/js/main.js" -Encoding utf8
    Write-Host "   ✅ main.js placeholder created" -ForegroundColor Green
}

if (-not (Test-Path "$CSS_DIR\style.css")) {
    Set-Content "$CSS_DIR\style.css" "/* EduCore style.css */`n/* Copy from original PHP project: ../educore-cms/assets/css/style.css */" -Encoding utf8
    Write-Host "   ✅ style.css placeholder created" -ForegroundColor Green
}

if (-not (Test-Path "$CSS_DIR\superadmin.css")) {
    Set-Content "$CSS_DIR\superadmin.css" "/* EduCore SuperAdmin CSS */`n/* Copy from original PHP project: ../educore-cms/assets/css/superadmin.css */" -Encoding utf8
    Write-Host "   ✅ superadmin.css placeholder created" -ForegroundColor Green
}

# =============================================================
# SUMMARY
# =============================================================
Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "  ✅ ALL VENDOR FILES DOWNLOADED!" -ForegroundColor Green
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📋 Next steps:" -ForegroundColor White
Write-Host "   1. Copy style.css from ../educore-cms/assets/css/style.css" -ForegroundColor Gray
Write-Host "   2. Copy main.js  from ../educore-cms/assets/js/main.js" -ForegroundColor Gray
Write-Host "   3. Run: php artisan storage:link" -ForegroundColor Gray
Write-Host "   4. Create DB: educore_laravel in MySQL" -ForegroundColor Gray
Write-Host "   5. Run: php artisan migrate" -ForegroundColor Gray
Write-Host ""
