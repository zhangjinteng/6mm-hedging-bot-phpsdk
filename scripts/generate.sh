#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

command -v protoc >/dev/null 2>&1 || {
  echo "protoc is required" >&2
  exit 1
}
command -v grpc_php_plugin >/dev/null 2>&1 || {
  echo "grpc_php_plugin is required" >&2
  exit 1
}

rm -rf "${ROOT_DIR}/src/Sixmm/HedgingBot/Proto"
protoc -I "${ROOT_DIR}/proto" \
  --php_out="${ROOT_DIR}/src" \
  --grpc_out="${ROOT_DIR}/src" \
  --plugin=protoc-gen-grpc="$(command -v grpc_php_plugin)" \
  "${ROOT_DIR}/proto/hedging/v1/hedging.proto"
