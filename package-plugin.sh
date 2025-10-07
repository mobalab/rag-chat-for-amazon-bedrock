#!/bin/bash

# WordPress Plugin Packaging Script
# Creates a clean ZIP package for WordPress.org submission

PLUGIN_NAME="wp-rag-ab"
PACKAGE_DIR="./package-temp"
PLUGIN_DIR="$PACKAGE_DIR/$PLUGIN_NAME"
ZIP_FILE="./${PLUGIN_NAME}.zip"

echo "🚀 Packaging WordPress plugin..."

# Remove existing package files
rm -rf "$PACKAGE_DIR"
rm -f "$ZIP_FILE"

# Create plugin directory structure
mkdir -p "$PLUGIN_DIR"

# Copy plugin files excluding development files
rsync -av \
  --exclude='.git*' \
  --exclude='CLAUDE.md' \
  --exclude='composer.json' \
  --exclude='composer.lock' \
  --exclude='vendor/' \
  --exclude='.idea/' \
  --exclude='package-temp/' \
  --exclude='package-plugin.sh' \
  --exclude="$PLUGIN_NAME.zip" \
  ./ "$PLUGIN_DIR/"

# Create ZIP package
cd "$PACKAGE_DIR" || exit 1
zip -r "../$ZIP_FILE" "$PLUGIN_NAME/" -x "*.DS_Store" "*/.claude/*"
cd ..

# Clean up temporary directory
rm -rf "$PACKAGE_DIR"

echo "✅ Plugin packaged successfully: $ZIP_FILE"
echo "📦 Ready for WordPress.org submission!"

# Show package contents
echo ""
echo "📋 Package contents:"
unzip -l "$ZIP_FILE" | head -20