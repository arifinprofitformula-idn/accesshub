param(
    [string]$Source = "public\images\accesshub-auth-logo.png",
    [string]$OutputDir = "public\icons"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

Add-Type -AssemblyName System.Drawing

if (-not (Test-Path $Source)) {
    throw "Source image not found: $Source"
}

New-Item -ItemType Directory -Force -Path $OutputDir | Out-Null

$iconSizes = @(72, 96, 128, 144, 152, 192, 384, 512)
$sourceImage = [System.Drawing.Image]::FromFile((Resolve-Path $Source))

function New-ResizedBitmap {
    param(
        [System.Drawing.Image]$Image,
        [int]$Size,
        [bool]$Maskable = $false,
        [string]$Badge = $null
    )

    $bitmap = New-Object System.Drawing.Bitmap($Size, $Size)
    $bitmap.SetResolution(72, 72)

    $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graphics.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
    $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $graphics.Clear([System.Drawing.Color]::FromArgb(2, 6, 23))

    $marginRatio = if ($Maskable) { 0.18 } else { 0.06 }
    $logoSize = [int]($Size * (1 - ($marginRatio * 2)))
    $logoOffset = [int](($Size - $logoSize) / 2)

    if ($Maskable) {
        $ellipsePath = New-Object System.Drawing.Drawing2D.GraphicsPath
        $ellipsePath.AddEllipse([int]($Size * 0.12), [int]($Size * 0.12), [int]($Size * 0.76), [int]($Size * 0.76))
        $glowBrush = New-Object System.Drawing.Drawing2D.PathGradientBrush($ellipsePath)
        $glowBrush.CenterColor = [System.Drawing.Color]::FromArgb(70, 34, 211, 238)
        $glowBrush.SurroundColors = @([System.Drawing.Color]::FromArgb(0, 34, 211, 238))
        $graphics.FillEllipse($glowBrush, [int]($Size * 0.12), [int]($Size * 0.12), [int]($Size * 0.76), [int]($Size * 0.76))
        $glowBrush.Dispose()
        $ellipsePath.Dispose()
    }

    $graphics.DrawImage($Image, $logoOffset, $logoOffset, $logoSize, $logoSize)

    if ($Badge) {
        $badgeSize = [int]($Size * 0.26)
        $badgeX = $Size - $badgeSize - [int]($Size * 0.08)
        $badgeY = $Size - $badgeSize - [int]($Size * 0.08)

        $badgeRect = New-Object System.Drawing.Rectangle($badgeX, $badgeY, $badgeSize, $badgeSize)
        $badgeRectF = New-Object System.Drawing.RectangleF([float]$badgeX, [float]$badgeY, [float]$badgeSize, [float]$badgeSize)
        $badgeBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(240, 15, 23, 42))
        $badgePen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(210, 125, 211, 252), [float]($Size * 0.015))
        $graphics.FillEllipse($badgeBrush, $badgeRect)
        $graphics.DrawEllipse($badgePen, $badgeRect)

        $fontSize = [float]($Size * 0.14)
        $font = New-Object System.Drawing.Font("Segoe UI Symbol", $fontSize, [System.Drawing.FontStyle]::Bold, [System.Drawing.GraphicsUnit]::Pixel)
        $textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(255, 224, 242, 254))
        $stringFormat = New-Object System.Drawing.StringFormat
        $stringFormat.Alignment = [System.Drawing.StringAlignment]::Center
        $stringFormat.LineAlignment = [System.Drawing.StringAlignment]::Center
        $graphics.DrawString($Badge, $font, $textBrush, $badgeRectF, $stringFormat)

        $stringFormat.Dispose()
        $textBrush.Dispose()
        $font.Dispose()
        $badgePen.Dispose()
        $badgeBrush.Dispose()
    }

    $graphics.Dispose()
    return $bitmap
}

try {
    foreach ($size in $iconSizes) {
        $bitmap = New-ResizedBitmap -Image $sourceImage -Size $size
        $bitmap.Save((Join-Path $OutputDir "icon-$size.png"), [System.Drawing.Imaging.ImageFormat]::Png)
        $bitmap.Dispose()
    }

    $maskableBitmap = New-ResizedBitmap -Image $sourceImage -Size 512 -Maskable $true
    $maskableBitmap.Save((Join-Path $OutputDir "icon-512-maskable.png"), [System.Drawing.Imaging.ImageFormat]::Png)
    $maskableBitmap.Dispose()

    $shortcutAdd = New-ResizedBitmap -Image $sourceImage -Size 192 -Badge "+"
    $shortcutAdd.Save((Join-Path $OutputDir "shortcut-add.png"), [System.Drawing.Imaging.ImageFormat]::Png)
    $shortcutAdd.Dispose()

    $shortcutStar = New-ResizedBitmap -Image $sourceImage -Size 192 -Badge "★"
    $shortcutStar.Save((Join-Path $OutputDir "shortcut-star.png"), [System.Drawing.Imaging.ImageFormat]::Png)
    $shortcutStar.Dispose()
}
finally {
    $sourceImage.Dispose()
}
