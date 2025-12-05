<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <title>Schuifpuzzel</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: linear-gradient(120deg, #f0f4ff, #ffaeaeff, #a5ffb8ff);
      background-size: 600% 600%;
      animation: bgMove 30s ease infinite;
    }

    @keyframes bgMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    h1 {
      color: #333;
      margin-bottom: 10px;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .controls {
      margin-bottom: 20px;
      display: flex;
      gap: 12px;
      align-items: center;
    }

    select, button {
      padding: 8px 12px;
      border-radius: 8px;
      border: none;
      background-color: #ffffffcc;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      font-size: 14px;
      cursor: pointer;
      transition: transform 0.2s;
    }

    select:hover, button:hover {
      transform: scale(1.05);
    }

    canvas {
      background: none;
    }

    .message {
      margin-top: 12px;
      font-weight: bold;
      color: #2e7d32;
    }

    .back-btn {
  position: absolute;
  left: 20px;
  top: 20px;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.85);
  border: none;
  border-radius: 10px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  backdrop-filter: blur(4px);
  transition: 0.25s ease;
}

.back-btn:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 1);
  box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

  </style>
</head>

<body>

<button class="back-btn" onclick="window.location.href='archiefbreed.php'">
  ⬅ Terug naar leporello
</button>


  <h1>Schuifpuzzel</h1>
  <div class="controls">
    <label for="grid">Aantal stukken:</label>
    <select id="grid">
      <option value="3" selected>3 × 3</option>
      <option value="4">4 × 4</option>
      <option value="5">5 × 5</option>
      <option value="6">6 × 6</option>
    </select>
    <button id="reset">Hussel opnieuw</button>
  </div>

  <canvas id="puzzle" width="600" height="600"></canvas>
  <div class="message" id="msg"></div>

  <script>
    const canvas = document.getElementById('puzzle');
    const ctx = canvas.getContext('2d');
    const gridSelect = document.getElementById('grid');
    const resetBtn = document.getElementById('reset');
    const msg = document.getElementById('msg');

    const N = () => parseInt(gridSelect.value);
    const SIZE = canvas.width;
    const pieceSize = () => SIZE / N();

    let img = new Image();
    img.src = 'assets/img/puzzelfoto.jpg';

    let pieces = [];
    let solved = false;
    let emptyCell = { row: 0, col: 0 };

    img.onload = () => {
      initPuzzle();
      shufflePuzzle();
      drawPuzzle();
    };

    gridSelect.addEventListener('change', () => {
      initPuzzle();
      shufflePuzzle();
      drawPuzzle();
    });

    function initPuzzle() {
      pieces = [];
      for (let r = 0; r < N(); r++) {
        for (let c = 0; c < N(); c++) {
          if (r === 0 && c === 0) {
            emptyCell = { row: 0, col: 0 };
          } else {
            pieces.push({
              id: r * N() + c,
              correctRow: r,
              correctCol: c,
              row: r,
              col: c
            });
          }
        }
      }
      solved = false;
    }

    function shufflePuzzle() {
      for (let i = 0; i < 200; i++) {
        const neighbors = pieces.filter(p =>
          (Math.abs(p.row - emptyCell.row) === 1 && p.col === emptyCell.col) ||
          (Math.abs(p.col - emptyCell.col) === 1 && p.row === emptyCell.row)
        );
        const rand = neighbors[Math.floor(Math.random() * neighbors.length)];
        [rand.row, rand.col, emptyCell.row, emptyCell.col] = [emptyCell.row, emptyCell.col, rand.row, rand.col];
      }
      solved = false;
      msg.textContent = '';
    }

    function drawPuzzle() {
      ctx.clearRect(0, 0, SIZE, SIZE);

      // transparante volledige foto als achtergrond
      ctx.globalAlpha = 1.0;
      ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, SIZE, SIZE);
      ctx.globalAlpha = 1;

      pieces.forEach(p => {
        const sx = p.correctCol * (img.width / N());
        const sy = p.correctRow * (img.height / N());
        const dx = p.col * pieceSize();
        const dy = p.row * pieceSize();
        ctx.drawImage(
          img,
          sx, sy,
          img.width / N(), img.height / N(),
          dx, dy, pieceSize(), pieceSize()
        );
        ctx.strokeStyle = '#333';
        ctx.lineWidth = 2;
        ctx.strokeRect(dx, dy, pieceSize(), pieceSize());
      });

      // lege cel tekenen
      ctx.fillStyle = "rgba(255,255,255,0.8)";
      ctx.fillRect(emptyCell.col * pieceSize(), emptyCell.row * pieceSize(), pieceSize(), pieceSize());
    }

    function getPieceAt(row, col) {
      return pieces.find(p => p.row === row && p.col === col);
    }

    canvas.addEventListener('click', e => {
      if (solved) return;

      const rect = canvas.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const col = Math.floor(x / pieceSize());
      const row = Math.floor(y / pieceSize());
      const p = getPieceAt(row, col);
      if (!p) return;

      const dx = Math.abs(p.col - emptyCell.col);
      const dy = Math.abs(p.row - emptyCell.row);

      if ((dx === 1 && dy === 0) || (dx === 0 && dy === 1)) {
        [p.row, p.col, emptyCell.row, emptyCell.col] = [emptyCell.row, emptyCell.col, p.row, p.col];
        drawPuzzle();
        checkSolved();
      }
    });

    function checkSolved() {
      solved = pieces.every(p => p.row === p.correctRow && p.col === p.correctCol) &&
        emptyCell.row === 0 && emptyCell.col === 0;
      if (solved) {
        msg.textContent = '🎉 Goed gedaan!';
        document.getElementById('popup').style.display = 'flex';
      }
    }

resetBtn.addEventListener('click', () => {
      shufflePuzzle();
      drawPuzzle();
      msg.textContent = 'Puzzel opnieuw gehusseld!';
    });
  </script>
  <div id="popup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(3px); align-items:center; justify-content:center;">
    <div style="background:white; padding:20px 30px; border-radius:12px; text-align:center; box-shadow:0 4px 15px rgba(0,0,0,0.3);">
      <h2>🎉 Puzzel voltooid!</h2>
      <button onclick="document.getElementById('popup').style.display='none'" style="margin-top:10px; padding:8px 16px; border:none; border-radius:6px; cursor:pointer;">Sluiten</button>
    </div>
  </div>
</body>
</html>
