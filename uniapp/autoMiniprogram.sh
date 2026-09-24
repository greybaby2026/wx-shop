#!/bin/bash
# ============================================================
# 发布小程序构建产物到 server/public/mp-weixin（供上传/预览用）
#
# ⚠️ 加固说明（批次3 FIX-U20）：与 autoRelease.sh 同型 ——
#   原脚本先 rm -r 生产目录再复制，源目录缺失时会清空线上目录且无回滚。
#   加固点：set -euo pipefail / 源目录校验 / 发布目录前缀断言 /
#           临时目录 + 原子替换 / 保留上一版本
# ============================================================
set -euo pipefail

# 文件原路径（相对 uniapp 目录执行）
srcPath="./unpackage/dist/build/mp-weixin"
# 发布路径文件夹
releasePath="../server/public/mp-weixin"
# 上一版本备份（回滚用）
backupPath="../server/public/mp-weixin_prev"
# 临时目录
tempPath="../server/public/mp-weixin_new"

# ---------- 1) 源目录校验（绝不先动生产目录） ----------
if [ ! -d "$srcPath" ]; then
    echo "[中止] 源目录不存在：$srcPath（请先在 HBuilderX 完成微信小程序构建）" >&2
    exit 1
fi
if [ -z "$(ls -A "$srcPath" 2>/dev/null || true)" ]; then
    echo "[中止] 源目录为空：$srcPath（构建产物缺失）" >&2
    exit 1
fi
# 小程序构建产物必备文件
if [ ! -f "$srcPath/app.json" ]; then
    echo "[中止] 源目录缺少 app.json，疑似构建不完整：$srcPath" >&2
    exit 1
fi

# ---------- 2) 发布目录前缀断言 ----------
releaseAbs="$(cd "$(dirname "$releasePath")" && pwd)/$(basename "$releasePath")"
case "$releaseAbs" in
    */server/public/mp-weixin)
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

# favicon 位于 server/public 下，沿用原逻辑（存在才复制）
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
