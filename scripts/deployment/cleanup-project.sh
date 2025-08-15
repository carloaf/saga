#!/bin/bash
# =============================================================================
# SAGA Project Cleanup Script
# Clean up temporary and development files
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"

cd "$PROJECT_ROOT"

log_info "Starting SAGA project cleanup..."
log_info "Project root: $PROJECT_ROOT"

# Files and directories to clean up
CLEANUP_ITEMS=(
    # Temporary files
    "abbreviation"
    "name" 
    "txt.txt"
    "remove_background.py"
    "create_outras_om.php"
    "Dockerfile.old"
    
    # Backup environment files (keep .env.example)
    ".env.example.saga"
    ".env.saga"
    
    # Build artifacts
    ".phpunit.result.cache"
    
    # Temporary directory
    "temp/"
    
    # Legacy saga directory (if it's not needed)
    # "saga/"  # Commented out - might contain important data
)

# Test views that should be in temp (will move to temp instead of delete)
TEST_VIEWS=(
    "resources/views/test-timezone.blade.php"
    "resources/views/test-layout.blade.php"
)

# Dry run by default
DRY_RUN=true
if [[ "${1}" == "--force" ]]; then
    DRY_RUN=false
    log_warning "Force mode enabled - files will be actually removed"
else
    log_info "Running in dry-run mode. Use --force to actually remove files"
fi

# Create temp directory if it doesn't exist
if [[ ! -d "temp" ]]; then
    if [[ "$DRY_RUN" == "false" ]]; then
        mkdir -p temp
        log_info "Created temp directory"
    else
        log_info "Would create temp directory"
    fi
fi

# Move test views to temp
for test_view in "${TEST_VIEWS[@]}"; do
    if [[ -f "$test_view" ]]; then
        filename=$(basename "$test_view")
        if [[ "$DRY_RUN" == "false" ]]; then
            mv "$test_view" "temp/$filename"
            log_info "Moved $test_view to temp/"
        else
            log_info "Would move $test_view to temp/"
        fi
    fi
done

# Remove cleanup items
for item in "${CLEANUP_ITEMS[@]}"; do
    if [[ -e "$item" ]]; then
        if [[ "$DRY_RUN" == "false" ]]; then
            if [[ -d "$item" ]]; then
                rm -rf "$item"
                log_info "Removed directory: $item"
            else
                rm -f "$item"
                log_info "Removed file: $item"
            fi
        else
            if [[ -d "$item" ]]; then
                log_info "Would remove directory: $item"
            else
                log_info "Would remove file: $item"
            fi
        fi
    fi
done

# Clean up node_modules and vendor (optional)
if [[ "${CLEAN_DEPS:-false}" == "true" ]]; then
    log_info "Cleaning dependencies..."
    
    for dep_dir in "node_modules" "vendor"; do
        if [[ -d "$dep_dir" ]]; then
            if [[ "$DRY_RUN" == "false" ]]; then
                rm -rf "$dep_dir"
                log_info "Removed $dep_dir"
            else
                log_info "Would remove $dep_dir"
            fi
        fi
    done
fi

# Clean Laravel caches
log_info "Cleaning Laravel caches..."
CACHE_DIRS=(
    "storage/framework/cache/data/*"
    "storage/framework/sessions/*"
    "storage/framework/views/*"
    "storage/logs/*.log"
    "bootstrap/cache/*.php"
)

for cache_pattern in "${CACHE_DIRS[@]}"; do
    if ls $cache_pattern 1> /dev/null 2>&1; then
        if [[ "$DRY_RUN" == "false" ]]; then
            rm -f $cache_pattern
            log_info "Cleaned cache: $cache_pattern"
        else
            log_info "Would clean cache: $cache_pattern"
        fi
    fi
done

# Show final status
log_info "Cleanup summary:"
echo ""

if [[ "$DRY_RUN" == "true" ]]; then
    log_warning "This was a dry run. No files were actually removed."
    log_info "Run with --force to actually perform cleanup"
    log_info "Run with CLEAN_DEPS=true to also remove node_modules and vendor"
else
    log_success "Cleanup completed successfully!"
fi

echo ""
log_info "Project structure after cleanup:"
ls -la "$PROJECT_ROOT" | head -20

echo ""
log_info "Disk usage in project:"
du -sh "$PROJECT_ROOT"

if [[ "$DRY_RUN" == "true" ]]; then
    echo ""
    log_info "To perform actual cleanup:"
    echo "  $0 --force"
    echo ""
    log_info "To also clean dependencies:"
    echo "  CLEAN_DEPS=true $0 --force"
fi
