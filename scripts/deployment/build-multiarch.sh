#!/bin/bash
# =============================================================================
# SAGA Multi-Architecture Build Script
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
REGISTRY="${REGISTRY:-}"
NAMESPACE="${NAMESPACE:-saga}"
IMAGE_NAME="${IMAGE_NAME:-app}"
TAG="${TAG:-latest}"
PLATFORMS="${PLATFORMS:-linux/amd64,linux/arm64}"
BUILDER_NAME="saga-builder"

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

# Parse arguments
PUSH=false
LOAD=false
CACHE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --push)
            PUSH=true
            shift
            ;;
        --load)
            LOAD=true
            shift
            ;;
        --cache)
            CACHE=true
            shift
            ;;
        --tag)
            TAG="$2"
            shift 2
            ;;
        --platforms)
            PLATFORMS="$2"
            shift 2
            ;;
        --registry)
            REGISTRY="$2"
            shift 2
            ;;
        -h|--help)
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --push           Push to registry"
            echo "  --load           Load to local Docker"
            echo "  --cache          Use build cache"
            echo "  --tag TAG        Image tag (default: latest)"
            echo "  --platforms      Target platforms (default: linux/amd64,linux/arm64)"
            echo "  --registry       Registry URL"
            echo "  -h, --help       Show this help"
            exit 0
            ;;
        *)
            log_error "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Construct full image name
if [[ -n "$REGISTRY" ]]; then
    FULL_IMAGE_NAME="${REGISTRY}/${NAMESPACE}/${IMAGE_NAME}:${TAG}"
else
    FULL_IMAGE_NAME="${NAMESPACE}/${IMAGE_NAME}:${TAG}"
fi

log_info "Starting multi-architecture build..."
log_info "Image: ${FULL_IMAGE_NAME}"
log_info "Platforms: ${PLATFORMS}"

# Check if buildx is available
if ! docker buildx version &> /dev/null; then
    log_error "Docker buildx is not available"
    exit 1
fi

# Create/use builder instance
log_info "Setting up buildx builder..."
if ! docker buildx inspect $BUILDER_NAME &> /dev/null; then
    log_info "Creating new builder instance: $BUILDER_NAME"
    docker buildx create --name $BUILDER_NAME --driver docker-container --bootstrap
fi

docker buildx use $BUILDER_NAME

# Prepare build arguments
BUILD_ARGS=(
    "--platform" "$PLATFORMS"
    "--tag" "$FULL_IMAGE_NAME"
    "--file" "Dockerfile"
)

# Add cache arguments if enabled
if [[ "$CACHE" == "true" ]]; then
    BUILD_ARGS+=(
        "--cache-from" "type=gha"
        "--cache-to" "type=gha,mode=max"
    )
fi

# Add push or load
if [[ "$PUSH" == "true" && "$LOAD" == "true" ]]; then
    log_error "Cannot use --push and --load together"
    exit 1
elif [[ "$PUSH" == "true" ]]; then
    BUILD_ARGS+=("--push")
    log_info "Will push to registry"
elif [[ "$LOAD" == "true" ]]; then
    BUILD_ARGS+=("--load")
    log_info "Will load to local Docker"
    # When loading, can only build for current platform
    BUILD_ARGS[1]="$(docker version --format '{{.Client.Os}}/{{.Client.Arch}}')"
fi

# Add current directory as context
BUILD_ARGS+=(".")

# Execute build
log_info "Building image..."
echo "Command: docker buildx build ${BUILD_ARGS[*]}"
docker buildx build "${BUILD_ARGS[@]}"

if [[ "$PUSH" == "true" ]]; then
    log_success "Multi-architecture image pushed successfully!"
    log_info "Image: ${FULL_IMAGE_NAME}"
    log_info "Platforms: ${PLATFORMS}"
    
    # Show manifest
    log_info "Manifest inspection:"
    docker buildx imagetools inspect "${FULL_IMAGE_NAME}"
elif [[ "$LOAD" == "true" ]]; then
    log_success "Image loaded to local Docker successfully!"
    log_info "Image: ${FULL_IMAGE_NAME}"
    
    # Show local images
    docker images | grep "${NAMESPACE}/${IMAGE_NAME}"
else
    log_success "Multi-architecture build completed successfully!"
    log_warning "Image was not pushed or loaded. Use --push or --load to save the image."
fi

log_success "Build process completed!"
