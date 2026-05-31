#!/usr/bin/env bash
set -euo pipefail

CLIPS=".tmp/clips"
AUD="C:/Users/User/Desktop/Claude/ClaudeCode/Aril/ProjectAI/SpecialProject/Video/Test01/unijaya-tutorial-pipeline/output/inpres_narration"
DUR_JSON="$AUD/durations.json"
WORK=".tmp/muxed"
OUT=".tmp/video/inpres-t-demo-narrated.mp4"
rm -rf "$WORK"; mkdir -p "$WORK" .tmp/video

# read id + duration pairs from durations.json (order preserved)
mapfile -t ROWS < <(node -e '
  const d=require("'"$DUR_JSON"'");
  for(const r of d) console.log(r.id+"|"+r.duration);
')

LIST=".tmp/muxed/list.txt"; : > "$LIST"
for row in "${ROWS[@]}"; do
  id="${row%%|*}"; dur="${row##*|}"
  vid="$CLIPS/$id.webm"
  aud="$AUD/$id.mp3"
  out="$WORK/$id.mp4"
  echo ">> $id  dur=$dur"
  ffmpeg -y -loglevel error -i "$vid" -i "$aud" \
    -filter_complex "[0:v]tpad=stop_mode=clone:stop_duration=3,fps=30,scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2,setsar=1,setpts=PTS-STARTPTS[v];[1:a]aresample=48000,asetpts=PTS-STARTPTS[a]" \
    -map "[v]" -map "[a]" -t "$dur" \
    -c:v libx264 -pix_fmt yuv420p -preset medium -crf 21 \
    -c:a aac -b:a 160k -ar 48000 -movflags +faststart "$out"
  echo "file '$(pwd)/$WORK/$id.mp4'" >> "$LIST"
done

echo ">> concat"
ffmpeg -y -loglevel error -f concat -safe 0 -i "$LIST" \
  -c:v libx264 -pix_fmt yuv420p -preset medium -crf 21 \
  -c:a aac -b:a 160k -ar 48000 -movflags +faststart "$OUT"

echo "OUT=$OUT"
ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "$OUT"
ls -lh "$OUT"
