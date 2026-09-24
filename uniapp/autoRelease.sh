#!/bin/bash
# ============================================================
# 发布 H5 构建产物到生产目录（server/public/mobile）
#
# ⚠️ 加固说明（批次3 FIX-U20）：原脚本为
#     rm -r $releasePath && mkdir $releasePath && cp -r $srcPath/* $releasePath
#   存在「源目录不存在/为空时，先把线上目录删掉、再复制失败」的风险 ——
#   即线上 H5 站点被清空且无回滚（生产事故级）。现加固：
#     1) set -euo pipefail：任一步失败立即中止
#     2) 校验源目录存在、非空、含 index.html（构建不完整直接中止）
#     3) 断言发布目录必须在 server/public 前缀下（防变量写错误删其它目录）
#     4) 先复制到临时目录，再原子替换；上一版本保留为 mobile_prev 以便回滚
# ============================================================
set -euo pipefail

# 文件原路径（相对 uniapp 目录执行）
srcPath="./unpackage/dist/build/web"
# 发布路径文件夹
releasePath="../server/public/mobile"
# 上一版本备份（回滚用）
backupPath="../server/public/mobile_prev"
# 临时目录
tempPath="../server/public/mobile_new"

# ---------- 1) 源目录校验（绝不先动生产目录） ----------
if [ ! -d "$srcPath" ]; then
    echo "[中止] 源目录不存在：$srcPath（请先在 HBuilderX 完成 H5 构建）" >&2
    exit 1
fi
if [ -z "$(ls -A "$srcPath" 2>/dev/null || true)" ]; then
    echo "[中止] 源目录为空：$srcPath（构建产物缺失）" >&2
    exit 1
fi
if [ ! -f "$srcPath/index.html" ]; then
    echo "[中止] 源目录缺少 index.html，疑似构建不完整：$srcPath" >&2
    exit 1
fi

# ---------- 2) 发布目录前缀断言 ----------
releaseAbs="$(cd "$(dirname "$releasePath")" && pwd)/$(basename "$releasePath")"
case "$releaseAbs" in
    */server/public/mobile)
        ;;
    *)
        echo "[中止] 发布目录不在预期前缀 server/public 下：$releaseAbs" >&2
        exit 1
        ;;
esac

# ---------- 3) 复制到临时目录 ----------
rm -rf "$tempPath"
mkdir -p "$tempPath"
cp -r "$srcPath"/. "$tempPath/"
echo "已复制 $srcPath/* ==> $tempPath"

# favicon 位于 server/public 下，沿用原逻辑复制进发布目录（存在才复制）
if [ -f "$(dirname "$releasePath")/favicon.ico" ]; then
    cp "$(dirname "$releasePath")/favicon.ico" "$tempPath/"
fi

# ---------- 4) 原子替换并保留上一版本 ----------
rm -rf "$backupPath"
if [ -d "$releasePath" ]; then
    mv "$releasePath" "$backupPath"
    echo "上一版本已备份 ==> $backupPath"
fi
mv "$tempPath" "$releasePath"
echo "已发布 ==> $releasePath"
echo "如需回滚：rm -rf $releasePath && mv $backupPath $releasePath"
