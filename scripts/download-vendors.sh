#!/usr/bin/env bash
# =============================================================
# EduCore CMS — Frontend Vendor Download Script
# Run once to download all vendor files locally
# Usage: bash scripts/download-vendors.sh
# =============================================================

set -e

VENDOR_DIR="public/assets/vendor"
CSS_DIR="public/assets/css"
JS_DIR="public/assets/js"

echo ""
echo "====================================================="
echo "  EduCore — Downloading Frontend Vendor Files"
echo "====================================================="
echo ""

# Create all directories
mkdir -p "$VENDOR_DIR/bootstrap"
mkdir -p "$VENDOR_DIR/fontawesome/webfonts"
mkdir -p "$VENDOR_DIR/chartjs"
mkdir -p "$VENDOR_DIR/datatables"
mkdir -p "$VENDOR_DIR/sweetalert2"
mkdir -p "$VENDOR_DIR/axios"
mkdir -p "$VENDOR_DIR/qrcodejs"
mkdir -p "$CSS_DIR"
mkdir -p "$JS_DIR"

echo "📁 Directories created."
echo ""

# =============================================================
# 1. BOOTSTRAP 5.3.3
# =============================================================
echo "⬇️  Bootstrap 5.3.3..."
curl -sL "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" \
     -o "$VENDOR_DIR/bootstrap/bootstrap.min.css"
curl -sL "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" \
     -o "$VENDOR_DIR/bootstrap/bootstrap.bundle.min.js"
echo "   ✅ Bootstrap done"

# =============================================================
# 2. FONT AWESOME 6.5.2
# =============================================================
echo "⬇️  Font Awesome 6.5.2..."
curl -sL "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" \
     -o "$VENDOR_DIR/fontawesome/all.min.css"

# Font Awesome webfonts
FA_BASE="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts"
for font in \
    "fa-brands-400.woff2" \
    "fa-brands-400.ttf" \
    "fa-regular-400.woff2" \
    "fa-regular-400.ttf" \
    "fa-solid-900.woff2" \
    "fa-solid-900.ttf" \
    "fa-v4compatibility.woff2" \
    "fa-v4compatibility.ttf"; do
    curl -sL "$FA_BASE/$font" -o "$VENDOR_DIR/fontawesome/webfonts/$font"
done

# Fix paths in CSS to point to local webfonts
sed -i 's|https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/|../fontawesome/webfonts/|g' \
    "$VENDOR_DIR/fontawesome/all.min.css"
echo "   ✅ Font Awesome done"

# =============================================================
# 3. CHART.JS 4.4.1
# =============================================================
echo "⬇️  Chart.js 4.4.1..."
curl -sL "https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" \
     -o "$VENDOR_DIR/chartjs/chart.umd.min.js"
echo "   ✅ Chart.js done"

# =============================================================
# 4. DATATABLES 2.x (Bootstrap 5 theme) + Buttons extension
# =============================================================
echo "⬇️  DataTables 2.x + Buttons..."

# DataTables core
curl -sL "https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" \
     -o "$VENDOR_DIR/datatables/dataTables.min.css"
curl -sL "https://cdn.datatables.net/2.0.8/js/dataTables.min.js" \
     -o "$VENDOR_DIR/datatables/dataTables.min.js"
curl -sL "https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js" \
     -o "$VENDOR_DIR/datatables/dataTables.bootstrap5.min.js"

# Buttons extension
curl -sL "https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css" \
     -o "$VENDOR_DIR/datatables/buttons.min.css"
curl -sL "https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js" \
     -o "$VENDOR_DIR/datatables/buttons.min.js"
curl -sL "https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js" \
     -o "$VENDOR_DIR/datatables/buttons.bootstrap5.min.js"
curl -sL "https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js" \
     -o "$VENDOR_DIR/datatables/buttons.html5.min.js"
curl -sL "https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js" \
     -o "$VENDOR_DIR/datatables/buttons.print.min.js"
echo "   ✅ DataTables done"

# =============================================================
# 5. SWEETALERT2
# =============================================================
echo "⬇️  SweetAlert2..."
curl -sL "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" \
     -o "$VENDOR_DIR/sweetalert2/sweetalert2.min.css"
curl -sL "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js" \
     -o "$VENDOR_DIR/sweetalert2/sweetalert2.min.js"
echo "   ✅ SweetAlert2 done"

# =============================================================
# 6. AXIOS 1.7.x
# =============================================================
echo "⬇️  Axios..."
curl -sL "https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js" \
     -o "$VENDOR_DIR/axios/axios.min.js"
echo "   ✅ Axios done"

# =============================================================
# 7. QRCODE.JS
# =============================================================
echo "⬇️  QRCode.js..."
curl -sL "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" \
     -o "$VENDOR_DIR/qrcodejs/qrcode.min.js"
echo "   ✅ QRCode.js done"

# =============================================================
# 8. JQUERY (required by DataTables)
# =============================================================
echo "⬇️  jQuery 3.7.1..."
mkdir -p "$VENDOR_DIR/jquery"
curl -sL "https://code.jquery.com/jquery-3.7.1.min.js" \
     -o "$VENDOR_DIR/jquery/jquery.min.js"
echo "   ✅ jQuery done"

# =============================================================
# 9. CREATE PLACEHOLDER app.js (global Ajax helpers)
# =============================================================
if [ ! -f "$JS_DIR/app.js" ]; then
    echo "📝  Creating placeholder app.js..."
    cat > "$JS_DIR/app.js" << 'APPJS'
// ============================================
// EDUCORE — Global Ajax + SweetAlert Helpers
// Will be populated during Phase 1 build
// ============================================
APPJS
    echo "   ✅ app.js placeholder created"
fi

echo ""
echo "====================================================="
echo "  ✅ ALL VENDOR FILES DOWNLOADED SUCCESSFULLY!"
echo "====================================================="
echo ""
echo "File sizes:"
du -sh "$VENDOR_DIR"/*/  2>/dev/null | sort -h
echo ""
echo "Next: Run 'php artisan storage:link' to create storage symlink"
echo "====================================================="
