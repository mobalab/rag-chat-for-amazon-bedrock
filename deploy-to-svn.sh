#!/bin/bash

# WordPress Plugin SVN Deployment Script
# Deploys plugin to WordPress.org SVN repository
# Usage: ./deploy-to-svn.sh [--version VERSION] [--username USERNAME]

PLUGIN_NAME="rag-chat-ab"
SVN_REPO="https://plugins.svn.wordpress.org/$PLUGIN_NAME"
SVN_DIR="./${PLUGIN_NAME}-svn"
TRUNK_DIR="$SVN_DIR/trunk"
VERSION=""
SVN_USERNAME="mobalabkashima"

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --version)
            VERSION="$2"
            shift 2
            ;;
        --username)
            SVN_USERNAME="$2"
            shift 2
            ;;
        *)
            echo "Unknown option: $1"
            echo "Usage: $0 [--version VERSION] [--username USERNAME]"
            exit 1
            ;;
    esac
done

# Ask for version if not provided
if [ -z "$VERSION" ]; then
    echo "🚀 Deploying plugin to WordPress.org SVN..."
    echo ""
    read -p "📝 Enter version number (e.g., 0.0.1): " VERSION

    if [ -z "$VERSION" ]; then
        echo "❌ Version number is required."
        exit 1
    fi
else
    echo "🚀 Deploying plugin version $VERSION to WordPress.org SVN..."
fi

# Check if SVN is available
if ! command -v svn &> /dev/null; then
    echo "❌ SVN is not installed. Please install Subversion."
    exit 1
fi

# Remove existing SVN directory
if [ -d "$SVN_DIR" ]; then
    echo "🧹 Removing existing SVN directory..."
    rm -rf "$SVN_DIR"
fi

# Checkout SVN repository
echo "📥 Checking out SVN repository..."
if [ -n "$SVN_USERNAME" ]; then
    svn co "$SVN_REPO" "$SVN_DIR" --username "$SVN_USERNAME"
else
    svn co "$SVN_REPO" "$SVN_DIR"
fi

if [ $? -ne 0 ]; then
    echo "❌ Failed to checkout SVN repository. Check your credentials."
    exit 1
fi

# Clean trunk directory
echo "🧹 Cleaning trunk directory..."
rm -rf "$TRUNK_DIR"/*

# Copy plugin files to trunk (excluding development files)
echo "📁 Copying plugin files to trunk..."
rsync -av \
  --exclude='.git*' \
  --exclude='CLAUDE.md' \
  --exclude='composer.json' \
  --exclude='composer.lock' \
  --exclude='vendor/' \
  --exclude='.idea/' \
  --exclude='package-temp/' \
  --exclude='package-plugin.sh' \
  --exclude='deploy-to-svn.sh' \
  --exclude="${PLUGIN_NAME}.zip" \
  --exclude="${PLUGIN_NAME}-svn/" \
  ./ "$TRUNK_DIR/"

# Add new files to SVN
echo "➕ Adding files to SVN..."
cd "$SVN_DIR" || exit 1
svn add --force trunk/*

# Create/update tag for this version
echo "🏷️ Creating tag for version $VERSION..."
if [ -d "tags/$VERSION" ]; then
    svn rm "tags/$VERSION"
fi
svn cp trunk "tags/$VERSION"

# Show SVN status
echo ""
echo "📋 SVN Status:"
svn status

# Prompt for commit
echo ""
read -p "🤔 Do you want to commit these changes? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "💾 Committing to SVN..."
    if [ -n "$SVN_USERNAME" ]; then
        svn ci -m "Deploy version $VERSION" --username "$SVN_USERNAME"
    else
        svn ci -m "Deploy version $VERSION"
    fi

    if [ $? -eq 0 ]; then
        echo "✅ Successfully deployed to WordPress.org!"
        echo "🌐 Your plugin will be available at: https://wordpress.org/plugins/$PLUGIN_NAME"
        echo "⏱️ It may take up to 72 hours for search results to update"
    else
        echo "❌ Commit failed. Please check the error messages above."
        exit 1
    fi
else
    echo "🚫 Deployment cancelled. You can commit later with:"
    echo "   cd $SVN_DIR && svn ci -m 'Deploy version $VERSION'"
fi

echo ""
echo "📂 SVN files are in: $SVN_DIR"
echo "💡 Remember to add '$SVN_DIR/' to your .gitignore"