<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Absensi RFID V2 — SMKN 1 Air Naningan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{
    --paper:#EAE6DA;
    --paper-2:#E1DCCC;
    --card:#F4F1E7;
    --ink:#221E17;
    --ink-soft:#4B4638;
    --navy:#22344A;
    --navy-2:#334D6B;
    --maroon:#6E2A2A;
    --maroon-2:#8A3838;
    --brass:#A47C3B;
    --brass-soft:#C9AE7C;
    --green:#33553D;
    --green-bg:#DCE6D6;
    --slate:#4A525B;
    --slate-bg:#DEE1DC;
    --amber:#8F5B15;
    --amber-bg:#EBDCBF;
    --line: rgba(34,30,23,0.16);
    --line-strong: rgba(34,30,23,0.32);
    --radius: 10px;
  }
  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    margin:0;
    background:var(--paper);
    color:var(--ink);
    font-family:'IBM Plex Sans', sans-serif;
    font-size:16px;
    line-height:1.6;
  }
  .display{font-family:'Fraunces', serif;}
  .mono{font-family:'IBM Plex Mono', monospace;}
  a{color:inherit;}
  .wrap{max-width:980px;margin:0 auto;padding:0 28px;}

  /* ---------- NAV ---------- */
  nav{
    position:sticky;top:0;z-index:50;
    background:rgba(234,230,218,0.92);
    backdrop-filter:blur(6px);
    border-bottom:1px solid var(--line);
  }
  .nav-inner{
    max-width:980px;margin:0 auto;padding:14px 28px;
    display:flex;align-items:center;justify-content:space-between;
    gap:16px;
  }
  .nav-brand{
    display:flex;align-items:baseline;gap:8px;
    font-family:'Fraunces', serif;font-weight:600;font-size:16px;
    white-space:nowrap;
  }
  .nav-brand .tag{
    font-family:'IBM Plex Mono', monospace;font-size:10.5px;
    color:var(--ink-soft);letter-spacing:.06em;
  }
  .nav-links{
    display:flex;gap:2px;flex-wrap:wrap;
    font-size:12.5px;
  }
  .nav-links a{
    text-decoration:none;color:var(--ink-soft);
    padding:6px 10px;border-radius:6px;
  }
  .nav-links a:hover{background:var(--paper-2);color:var(--ink);}

  /* ---------- HERO ---------- */
  header.hero{
    position:relative;
    padding:76px 0 56px;
    border-bottom:1px solid var(--line-strong);
    overflow:hidden;
  }
  .hero-eyebrow{
    font-family:'IBM Plex Mono', monospace;
    font-size:12px;letter-spacing:.08em;text-transform:uppercase;
    color:var(--maroon);margin:0 0 18px;
    display:flex;align-items:center;gap:10px;
  }
  .hero-eyebrow::before{
    content:"";width:22px;height:1px;background:var(--maroon);display:inline-block;
  }
  h1.hero-title{
    font-size:clamp(40px, 6.4vw, 68px);
    line-height:0.98;
    font-weight:600;
    margin:0 0 20px;
    max-width:12ch;
    letter-spacing:-0.01em;
  }
  .hero-title em{font-style:italic;font-weight:400;color:var(--navy-2);}
  .hero-sub{
    max-width:58ch;font-size:17.5px;color:var(--ink-soft);margin:0 0 30px;
  }
  .hero-meta{
    display:flex;flex-wrap:wrap;gap:10px 26px;
    font-family:'IBM Plex Mono', monospace;font-size:12px;
    color:var(--ink-soft);
    border-top:1px solid var(--line);padding-top:18px;max-width:640px;
  }
  .hero-meta b{color:var(--ink);font-weight:500;}

  /* stamp */
  .stamp{
    position:absolute;top:56px;right:28px;
    width:132px;height:132px;
    border:1.5px solid var(--maroon);
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    transform:rotate(-9deg);
    color:var(--maroon);
    opacity:.9;
  }
  .stamp-inner{
    width:108px;height:108px;border:1px dashed var(--maroon);border-radius:50%;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    text-align:center;font-family:'IBM Plex Mono', monospace;
  }
  .stamp-inner .n{font-size:22px;font-weight:600;line-height:1;}
  .stamp-inner .l1{font-size:8.5px;letter-spacing:.05em;text-transform:uppercase;margin-top:4px;}
  .stamp-inner .l2{font-size:8px;letter-spacing:.05em;text-transform:uppercase;color:var(--maroon-2);}

  @media (max-width: 720px){ .stamp{display:none;} }

  /* ---------- SECTION GENERIC ---------- */
  section{padding:64px 0;border-bottom:1px solid var(--line);}
  section:last-of-type{border-bottom:none;}
  .sec-head{display:flex;align-items:baseline;gap:14px;margin-bottom:8px;}
  .sec-num{font-family:'IBM Plex Mono', monospace;font-size:12px;color:var(--brass);}
  h2.sec-title{
    font-family:'Fraunces', serif;font-weight:600;font-size:30px;margin:0;
  }
  .sec-lede{color:var(--ink-soft);max-width:62ch;margin:14px 0 34px;font-size:15.5px;}

  /* ---------- LEDGER (keputusan domain) ---------- */
  .ledger{
    background:var(--card);border:1px solid var(--line);border-radius:12px;
    overflow:hidden;
  }
  .ledger-row{
    display:grid;grid-template-columns:38px 1fr 118px;gap:16px;
    padding:15px 20px;border-bottom:1px solid var(--line);align-items:center;
  }
  .ledger-row:last-child{border-bottom:none;}
  .ledger-row .num{font-family:'IBM Plex Mono', monospace;color:var(--brass);font-size:13px;}
  .ledger-row .txt{font-size:14.5px;}
  .pill{
    justify-self:end;font-family:'IBM Plex Mono', monospace;font-size:10.5px;
    letter-spacing:.05em;text-transform:uppercase;
    padding:4px 9px;border-radius:20px;white-space:nowrap;
  }
  .pill.locked{background:var(--maroon);color:#F4EFE4;}

  /* ---------- PROBLEM/SOLUTION CARDS ---------- */
  .ps-grid{display:flex;flex-direction:column;gap:1px;background:var(--line);border:1px solid var(--line);border-radius:12px;overflow:hidden;}
  .ps-row{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--line);}
  .ps-cell{background:var(--card);padding:18px 20px;}
  .ps-cell .k{font-family:'IBM Plex Mono', monospace;font-size:10.5px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:8px;display:block;}
  .ps-cell.problem .k{color:var(--maroon);}
  .ps-cell.solution .k{color:var(--green);}
  .ps-cell p{margin:0;font-size:14px;}
  @media (max-width:640px){ .ps-row{grid-template-columns:1fr;} }

  /* ---------- STATE DIAGRAM ---------- */
  .state-wrap{
    background:var(--card);border:1px solid var(--line);border-radius:14px;
    padding:36px 30px;
  }
  .state-row{display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;}
  .state-node{
    font-family:'IBM Plex Mono', monospace;font-size:12.5px;
    padding:12px 18px;border-radius:8px;border:1px solid var(--line-strong);
    text-align:center;min-width:118px;
  }
  .state-node .st{display:block;font-size:13.5px;font-weight:500;margin-bottom:3px;}
  .state-node .sd{display:block;font-size:10px;color:var(--ink-soft);}
  .state-node.active{background:var(--green-bg);border-color:var(--green);color:var(--green);}
  .state-node.loop{background:var(--amber-bg);border-color:var(--amber);color:var(--amber);}
  .state-node.terminal{background:var(--slate-bg);border-color:var(--slate);color:var(--slate);}
  .arrow{font-size:16px;color:var(--ink-soft);}
  .loop-label{
    text-align:center;font-family:'IBM Plex Mono', monospace;font-size:10.5px;
    color:var(--amber);margin:10px 0 22px;letter-spacing:.03em;
  }
  .terminal-branch{display:flex;justify-content:center;gap:22px;flex-wrap:wrap;margin-top:8px;}
  .rfid-note{
    margin-top:28px;padding-top:22px;border-top:1px dashed var(--line-strong);
    display:flex;gap:14px;align-items:flex-start;font-size:13.5px;color:var(--ink-soft);
  }
  .rfid-note .tag{
    font-family:'IBM Plex Mono', monospace;font-size:10.5px;color:var(--maroon);
    border:1px solid var(--maroon);padding:3px 8px;border-radius:5px;white-space:nowrap;
  }

  /* ---------- SCAN RULES ---------- */
  .scan-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:28px;}
  @media (max-width:760px){.scan-grid{grid-template-columns:1fr;}}
  .receipt{
    background:var(--ink);color:var(--paper);border-radius:12px;padding:26px 24px;
    font-family:'IBM Plex Mono', monospace;font-size:12.5px;line-height:1.85;
  }
  .receipt .rt{color:var(--brass-soft);font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:14px;}
  .receipt .ind{padding-left:1.4em;}
  .rules-list{list-style:none;margin:0;padding:0;}
  .rules-list li{
    padding:13px 0;border-bottom:1px solid var(--line);font-size:14px;
    display:flex;gap:12px;
  }
  .rules-list li:last-child{border-bottom:none;}
  .rules-list li::before{content:"—";color:var(--brass);flex-shrink:0;}

  /* ---------- ACTORS ---------- */
  .actor-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
  @media (max-width:760px){.actor-grid{grid-template-columns:repeat(2,1fr);}}
  @media (max-width:480px){.actor-grid{grid-template-columns:1fr;}}
  .actor-card{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:18px;}
  .actor-card h3{font-family:'Fraunces', serif;font-weight:600;font-size:16.5px;margin:0 0 6px;}
  .actor-card .role{font-size:12.5px;color:var(--ink-soft);margin:0 0 10px;}
  .actor-card .acc{font-family:'IBM Plex Mono', monospace;font-size:11px;color:var(--navy-2);margin:0;line-height:1.6;}

  /* ---------- MODULES ---------- */
  .mod-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--line);border:1px solid var(--line);border-radius:12px;overflow:hidden;}
  @media (max-width:720px){.mod-grid{grid-template-columns:repeat(2,1fr);}}
  @media (max-width:480px){.mod-grid{grid-template-columns:1fr;}}
  .mod-cell{background:var(--card);padding:20px;}
  .mod-cell h4{font-family:'Fraunces', serif;font-size:16px;font-weight:600;margin:0 0 8px;}
  .mod-cell p{margin:0;font-size:13px;color:var(--ink-soft);}

  /* ---------- DATA MODEL ---------- */
  .data-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
  @media (max-width:680px){.data-grid{grid-template-columns:1fr;}}
  .data-card{
    background:var(--card);border:1px solid var(--line);border-radius:10px;
    padding:16px 18px;
  }
  .data-card .tbl{font-family:'IBM Plex Mono', monospace;font-size:12.5px;color:var(--navy);font-weight:500;margin-bottom:4px;}
  .data-card .role{font-size:12.5px;color:var(--ink-soft);margin-bottom:10px;}
  .data-card .fields{font-family:'IBM Plex Mono', monospace;font-size:10.5px;color:var(--brass);line-height:1.7;word-break:break-word;}

  /* ---------- SCENARIO TABLE ---------- */
  .scenario{width:100%;border-collapse:collapse;font-size:13.5px;}
  .scenario th, .scenario td{
    text-align:left;padding:12px 14px;border-bottom:1px solid var(--line);vertical-align:top;
  }
  .scenario th{
    font-family:'IBM Plex Mono', monospace;font-size:10.5px;text-transform:uppercase;
    letter-spacing:.05em;color:var(--ink-soft);font-weight:500;
  }
  .scenario td.sc-name{font-weight:500;font-family:'Fraunces', serif;}
  .scenario-wrap{overflow-x:auto;border:1px solid var(--line);border-radius:12px;background:var(--card);}
  .scenario-wrap table{margin:0;}
  .scenario-wrap td, .scenario-wrap th{white-space:normal;}

  /* ---------- TIMELINE ---------- */
  .tl{position:relative;padding-left:34px;}
  .tl::before{content:"";position:absolute;left:9px;top:6px;bottom:6px;width:1px;background:var(--line-strong);}
  .tl-item{position:relative;padding-bottom:30px;}
  .tl-item:last-child{padding-bottom:0;}
  .tl-item::before{
    content:"";position:absolute;left:-34px;top:4px;width:10px;height:10px;border-radius:50%;
    background:var(--paper);border:2px solid var(--navy);
  }
  .tl-period{font-family:'IBM Plex Mono', monospace;font-size:11.5px;color:var(--navy-2);margin-bottom:4px;}
  .tl-focus{font-family:'Fraunces', serif;font-weight:600;font-size:17px;margin:0 0 6px;}
  .tl-output{font-size:13.5px;color:var(--ink-soft);margin:0;}
  .gate-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:36px;}
  .gate{
    font-family:'IBM Plex Mono', monospace;font-size:11px;background:var(--amber-bg);color:var(--amber);
    border:1px solid var(--amber);border-radius:20px;padding:6px 12px;
  }

  /* ---------- READINESS ---------- */
  .ready-list{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:1fr 1fr;gap:12px;}
  @media (max-width:680px){.ready-list{grid-template-columns:1fr;}}
  .ready-list li{
    display:flex;gap:10px;align-items:flex-start;font-size:14px;
    background:var(--card);border:1px solid var(--line);border-radius:10px;padding:13px 15px;
  }
  .ready-list li::before{
    content:"";flex-shrink:0;width:16px;height:16px;margin-top:2px;border-radius:4px;
    border:1.5px solid var(--green);background:var(--green-bg);
  }

  /* ---------- FOOTER ---------- */
  footer{padding:48px 0 60px;}
  .footer-grid{display:flex;justify-content:space-between;gap:30px;flex-wrap:wrap;}
  .foot-files{font-family:'IBM Plex Mono', monospace;font-size:11.5px;color:var(--ink-soft);line-height:2;}
  .foot-note{max-width:34ch;font-size:12.5px;color:var(--ink-soft);}
  .foot-note .k{color:var(--maroon);font-family:'IBM Plex Mono', monospace;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;}
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <div class="nav-brand">Absensi RFID <span class="tag">V2 · SMKN 1 AIR NANINGAN</span></div>
    <div class="nav-links">
      <a href="#ringkasan">Ringkasan</a>
      <a href="#masalah">Masalah</a>
      <a href="#siklus">Siklus siswa</a>
      <a href="#absensi">Absensi</a>
      <a href="#data">Data</a>
      <a href="#timeline">Timeline</a>
      <a href="#siap">Kesiapan</a>
    </div>
  </div>
</nav>

<header class="hero">
  <div class="wrap">
    <p class="hero-eyebrow">Paket revisi prototype · 14 Agustus 2026</p>
    <h1 class="hero-title display">Absensi RFID yang <em>mengikuti</em> siklus akademik siswa</h1>
    <p class="hero-sub">Kelas bukan lagi atribut tetap pada siswa. Naik kelas, tinggal kelas, lulus, pindah, dan keluar kini tercatat sebagai riwayat — tanpa pernah menghapus siswa atau merusak histori absensi lama.</p>
    <div class="hero-meta">
      <span><b>Prototype</b> V2.0</span>
      <span><b>Baseline</b> v4</span>
      <span><b>UML</b> V2.0</span>
      <span><b>Roadmap</b> v2</span>
      <span><b>Fokus</b> rombel · RFID · histori absensi</span>
    </div>
  </div>
  <div class="stamp">
    <div class="stamp-inner">
      <span class="n">8</span>
      <span class="l1">Keputusan</span>
      <span class="l2">Dikunci</span>
    </div>
  </div>
</header>

<section id="ringkasan">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">01</span><h2 class="sec-title">Ringkasan &amp; keputusan domain</h2></div>
    <p class="sec-lede">Sumber kebenaran kelas dipindahkan dari tabel siswa ke riwayat <code class="mono">siswa_rombel</code>. Delapan keputusan berikut mengunci arah rancangan agar prototype, UML, baseline teknis, dan kode tetap selaras.</p>

    <div class="ledger">
      <div class="ledger-row"><span class="num">01</span><span class="txt">Kelas siswa dibaca dari <code class="mono">siswa_rombel</code>, bukan dari <code class="mono">class_id</code>/<code class="mono">kelas</code> pada siswa.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">02</span><span class="txt">Naik dan tinggal kelas menutup membership lama, lalu membuat membership tahun ajaran baru.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">03</span><span class="txt">Lulus, pindah, dan keluar tidak pernah menghapus siswa maupun histori mereka.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">04</span><span class="txt">Kartu RFID aktif dinonaktifkan begitu siswa memasuki status terminal.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">05</span><span class="txt">Absensi siswa menyimpan <code class="mono">id_siswa_rombel</code> sebagai snapshot kelas saat absensi terjadi.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">06</span><span class="txt">Scan siswa hanya sah bila siswa aktif dan memiliki membership aktif pada tahun ajaran aktif.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">07</span><span class="txt">Satu orang per tanggal hanya memiliki satu record absensi; scan pulang adalah pembaruan, bukan record baru.</span><span class="pill locked">Dikunci</span></div>
      <div class="ledger-row"><span class="num">08</span><span class="txt">Status standar untuk tidak hadir adalah <code class="mono">alpha</code>; sumber audit tunggal adalah <code class="mono">sumber_absen</code>.</span><span class="pill locked">Dikunci</span></div>
    </div>
  </div>
</section>

<section id="masalah">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">02</span><h2 class="sec-title">Masalah rancangan lama → solusi V2</h2></div>
    <p class="sec-lede">Enam masalah pada rancangan sebelumnya yang mendorong revisi ini, dan bagaimana V2 menutupnya.</p>

    <div class="ps-grid">
      <div class="ps-row">
        <div class="ps-cell problem"><span class="k">Masalah</span><p><code class="mono">class_id</code>/<code class="mono">kelas</code> langsung pada siswa</p></div>
        <div class="ps-cell solution"><span class="k">Solusi V2</span><p>Gunakan <code class="mono">rombel</code> + <code class="mono">siswa_rombel</code> per tahun ajaran — riwayat kelas lama tidak lagi tertimpa.</p></div>
      </div>
      <div class="ps-row">
        <div class="ps-cell problem"><span class="k">Masalah</span><p>Siswa lulus masih dianggap aktif</p></div>
        <div class="ps-cell solution"><span class="k">Solusi V2</span><p>Status siswa menjadi <code class="mono">lulus</code>; kartu aktif dinonaktifkan otomatis.</p></div>
      </div>
      <div class="ps-row">
        <div class="ps-cell problem"><span class="k">Masalah</span><p>Kenaikan kelas dilakukan dengan overwrite</p></div>
        <div class="ps-cell solution"><span class="k">Solusi V2</span><p>Tutup membership lama, buat membership baru — laporan tahun sebelumnya tidak ikut berubah.</p></div>
      </div>
      <div class="ps-row">
        <div class="ps-cell problem"><span class="k">Masalah</span><p>Absensi tidak menyimpan konteks kelas</p></div>
        <div class="ps-cell solution"><span class="k">Solusi V2</span><p>Absensi menyimpan <code class="mono">id_siswa_rombel</code> sebagai snapshot konteks saat kejadian.</p></div>
      </div>
      <div class="ps-row">
        <div class="ps-cell problem"><span class="k">Masalah</span><p>Kartu dihapus saat tidak dipakai</p></div>
        <div class="ps-cell solution"><span class="k">Solusi V2</span><p>Kartu dinonaktifkan, tidak dihapus — audit kartu tetap utuh.</p></div>
      </div>
      <div class="ps-row">
        <div class="ps-cell problem"><span class="k">Masalah</span><p>Penulisan status <code class="mono">alpa</code>/<code class="mono">alpha</code> tidak konsisten</p></div>
        <div class="ps-cell solution"><span class="k">Solusi V2</span><p>Satu kamus status baku: <code class="mono">alpha</code>.</p></div>
      </div>
    </div>
  </div>
</section>

<section id="siklus">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">03</span><h2 class="sec-title">Model siklus akademik siswa</h2></div>
    <p class="sec-lede">Status siswa adalah mesin keadaan sederhana: satu keadaan berjalan yang bisa berulang setiap tahun ajaran, dan tiga keadaan akhir yang menutup akses RFID untuk selamanya.</p>

    <div class="state-wrap">
      <div class="state-row">
        <div class="state-node active"><span class="st">aktif</span><span class="sd">boleh scan RFID</span></div>
      </div>
      <div class="loop-label">↻ naik kelas / tinggal kelas — tutup membership lama, buka membership baru, tetap aktif</div>
      <div class="state-row" style="margin-bottom:22px;">
        <div class="state-node loop"><span class="st">naik</span><span class="sd">siswa_rombel</span></div>
        <span class="arrow">·</span>
        <div class="state-node loop"><span class="st">tinggal</span><span class="sd">siswa_rombel</span></div>
      </div>
      <div class="loop-label">↓ status terminal — kartu RFID aktif dinonaktifkan, histori tetap dapat dilaporkan</div>
      <div class="terminal-branch">
        <div class="state-node terminal"><span class="st">lulus</span><span class="sd">tidak dapat scan</span></div>
        <div class="state-node terminal"><span class="st">pindah</span><span class="sd">tidak dapat scan</span></div>
        <div class="state-node terminal"><span class="st">keluar</span><span class="sd">tidak dapat scan</span></div>
      </div>
      <div class="rfid-note">
        <span class="tag">Aturan kartu</span>
        <span>RFID hanya menjadi identitas dan pemicu event. Status siswa, rombel aktif, waktu, dan izin ditentukan oleh server — terminal tidak pernah menjadi sumber kebenaran akademik.</span>
      </div>
    </div>
  </div>
</section>

<section id="absensi">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">04</span><h2 class="sec-title">Aturan bisnis scan &amp; absensi</h2></div>
    <p class="sec-lede">Validasi terjadi di server, dalam satu transaksi, sebelum record absensi dibuat atau diperbarui.</p>

    <div class="scan-grid">
      <div class="receipt">
        <div class="rt">Pseudocode — RfidScanService</div>
        scan(uid, now):<br>
        <span class="ind">card = kartu_rfid aktif berdasarkan uid</span><br>
        <span class="ind">owner = tepat satu: siswa atau guru</span><br>
        jika siswa:<br>
        <span class="ind">pastikan siswa.status = aktif</span><br>
        <span class="ind">membership = siswa_rombel aktif pada tahun_ajaran aktif</span><br>
        <span class="ind">gagal bila membership tidak ada</span><br>
        <span class="ind">attendance = absensi pemilik + tanggal [lock]</span><br>
        <span class="ind">jika belum ada: INSERT jam_masuk + snapshot membership</span><br>
        <span class="ind">jika jam_pulang kosong: UPDATE jam_pulang</span><br>
        <span class="ind">selain itu: absensi hari ini sudah lengkap</span>
      </div>
      <ul class="rules-list">
        <li>Satu orang hanya memiliki satu record absensi per tanggal; scan kedua mengisi <code class="mono">jam_pulang</code> pada record yang sama.</li>
        <li>Siswa aktif tanpa rombel aktif pada tahun ajaran aktif ditolak, agar absensi tidak kehilangan konteks kelas.</li>
        <li>Siswa lulus, pindah, atau keluar tidak dapat membentuk absensi baru.</li>
        <li>Guru/pegawai menggunakan <code class="mono">id_guru</code> dan tidak membutuhkan relasi <code class="mono">siswa_rombel</code>.</li>
      </ul>
    </div>
  </div>
</section>

<section id="aktor">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">05</span><h2 class="sec-title">Aktor &amp; hak akses</h2></div>
    <p class="sec-lede">Enam peran, dari konfigurasi sistem hingga penggunaan kartu sehari-hari.</p>
    <div class="actor-grid">
      <div class="actor-card"><h3 class="display">Super admin</h3><p class="role">Konfigurasi &amp; keamanan</p><p class="acc">Seluruh modul, pengguna, audit, perangkat</p></div>
      <div class="actor-card"><h3 class="display">Admin / TU</h3><p class="role">Administrasi akademik &amp; kehadiran</p><p class="acc">Siswa, rombel, tahun ajaran, transisi siswa, RFID, laporan</p></div>
      <div class="actor-card"><h3 class="display">Guru piket</h3><p class="role">Operasional kehadiran siswa</p><p class="acc">Terlambat, izin, pulang awal, indikasi bolos, koreksi terbatas</p></div>
      <div class="actor-card"><h3 class="display">Pimpinan</h3><p class="role">Monitoring</p><p class="acc">Dashboard &amp; laporan baca-saja</p></div>
      <div class="actor-card"><h3 class="display">Guru / pegawai</h3><p class="role">Pengguna kartu</p><p class="acc">Scan &amp; riwayat pribadi bila portal aktif</p></div>
      <div class="actor-card"><h3 class="display">Siswa</h3><p class="role">Pengguna kartu</p><p class="acc">Scan; tidak dapat mengubah status akademik atau absensi sendiri</p></div>
    </div>
  </div>
</section>

<section id="modul">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">06</span><h2 class="sec-title">Modul &amp; struktur menu</h2></div>
    <div class="mod-grid">
      <div class="mod-cell"><h4 class="display">Dashboard</h4><p>Statistik harian, status aktif, aktivitas RFID, alert.</p></div>
      <div class="mod-cell"><h4 class="display">Master akademik</h4><p>Tahun ajaran, jurusan, rombel, siswa, guru/pegawai.</p></div>
      <div class="mod-cell"><h4 class="display">Siklus siswa</h4><p>Naik kelas, tinggal kelas, lulus, pindah, keluar, histori rombel.</p></div>
      <div class="mod-cell"><h4 class="display">RFID</h4><p>Kartu, registrasi, penonaktifan, reader, log scan.</p></div>
      <div class="mod-cell"><h4 class="display">Absensi</h4><p>Siswa/guru, detail event, koreksi, sumber absensi.</p></div>
      <div class="mod-cell"><h4 class="display">Perizinan</h4><p>Izin, sakit, dispensasi, pulang awal, keluar sementara.</p></div>
      <div class="mod-cell"><h4 class="display">Guru piket</h4><p>Terlambat, izin, indikasi bolos, verifikasi.</p></div>
      <div class="mod-cell"><h4 class="display">Laporan</h4><p>Per individu, rombel, jurusan, periode, status akademik.</p></div>
      <div class="mod-cell"><h4 class="display">Audit</h4><p>Perubahan data, transisi akademik, koreksi absensi, kartu.</p></div>
    </div>
  </div>
</section>

<section id="data">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">07</span><h2 class="sec-title">Model data V2</h2></div>
    <p class="sec-lede">Sembilan tabel inti. <code class="mono">siswa_rombel</code> adalah penghubung historis antara siswa, rombel, dan tahun ajaran.</p>
    <div class="data-grid">
      <div class="data-card"><div class="tbl">siswa</div><div class="role">Identitas siswa jangka panjang</div><div class="fields">id_siswa · nis · nisn · nama · status</div></div>
      <div class="data-card"><div class="tbl">tahun_ajaran</div><div class="role">Periode akademik</div><div class="fields">id_tahun_ajaran · nama · is_active</div></div>
      <div class="data-card"><div class="tbl">jurusan</div><div class="role">Master kompetensi/jurusan</div><div class="fields">id_jurusan · kode_jurusan · nama_jurusan</div></div>
      <div class="data-card"><div class="tbl">rombel</div><div class="role">Kelas per tahun ajaran</div><div class="fields">id_rombel · id_tahun_ajaran · id_jurusan · nama_rombel · tingkat</div></div>
      <div class="data-card"><div class="tbl">siswa_rombel</div><div class="role">Histori keanggotaan kelas</div><div class="fields">id_siswa_rombel · id_siswa · id_rombel · id_tahun_ajaran · status_keanggotaan</div></div>
      <div class="data-card"><div class="tbl">kartu_rfid</div><div class="role">Identitas fisik RFID</div><div class="fields">uid · pemilik · status · tanggal_nonaktif</div></div>
      <div class="data-card"><div class="tbl">absensi</div><div class="role">Histori harian</div><div class="fields">pemilik · id_siswa_rombel · tanggal · jam · status · sumber_absen</div></div>
      <div class="data-card"><div class="tbl">izin_siswa</div><div class="role">Izin harian</div><div class="fields">id_siswa · tanggal · jenis · status</div></div>
    </div>
  </div>
</section>

<section id="skenario">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">08</span><h2 class="sec-title">Skenario transisi akademik</h2></div>
    <div class="scenario-wrap">
      <table class="scenario">
        <tr><th>Skenario</th><th>Sebelum</th><th>Aksi</th><th>Sesudah</th></tr>
        <tr><td class="sc-name">Naik kelas</td><td>X TKJ 1 / 2025–2026 aktif</td><td>Tutup membership lama = naik; buat XI TKJ 1 / 2026–2027</td><td>Siswa tetap aktif; histori X tetap ada</td></tr>
        <tr><td class="sc-name">Tinggal kelas</td><td>X TKJ 1 aktif</td><td>Tutup membership = tinggal; buat membership baru tingkat X</td><td>Siswa aktif; histori tahun lama tetap ada</td></tr>
        <tr><td class="sc-name">Lulus</td><td>XII TKJ 1 aktif</td><td>Tutup membership = lulus; status siswa = lulus; RFID nonaktif</td><td>Alumni tetap dapat dilaporkan; tidak dapat scan</td></tr>
        <tr><td class="sc-name">Pindah</td><td>Rombel aktif</td><td>Tutup membership = pindah; status siswa = pindah; RFID nonaktif</td><td>Histori tetap utuh</td></tr>
        <tr><td class="sc-name">Keluar</td><td>Rombel aktif</td><td>Tutup membership = keluar; status siswa = keluar; RFID nonaktif</td><td>Histori tetap utuh</td></tr>
      </table>
    </div>
  </div>
</section>

<section id="timeline">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">09</span><h2 class="sec-title">Timeline 6 minggu</h2></div>
    <p class="sec-lede">Urutan kerja mengikuti ketergantungan data: legacy dan waktu dulu, baru mesin absensi, baru laporan.</p>

    <div class="tl">
      <div class="tl-item"><div class="tl-period">17–21 Agustus</div><div class="tl-focus display">Validasi data &amp; migrasi legacy</div><p class="tl-output">Backup; mapping kelas lama; keputusan jam sekolah; skema produksi diverifikasi.</p></div>
      <div class="tl-item"><div class="tl-period">24–28 Agustus</div><div class="tl-focus display">Master akademik &amp; siklus siswa</div><p class="tl-output">CRUD tahun ajaran/jurusan/rombel; siswa_rombel; layanan naik/tinggal/lulus/pindah/keluar.</p></div>
      <div class="tl-item"><div class="tl-period">31 Agustus – 4 September</div><div class="tl-focus display">Mesin absensi RFID</div><p class="tl-output">RfidScanService + aturan_absensi; guru/siswa; snapshot rombel; debounce/idempotensi.</p></div>
      <div class="tl-item"><div class="tl-period">7–11 September</div><div class="tl-focus display">Izin &amp; evaluasi otomatis</div><p class="tl-output">Izin; status_final; scheduler alpha/bolos; Guru Piket.</p></div>
      <div class="tl-item"><div class="tl-period">14–18 September</div><div class="tl-focus display">Laporan, audit, keamanan</div><p class="tl-output">Laporan historis rombel; audit transisi; auth admin/perangkat; test lengkap.</p></div>
      <div class="tl-item"><div class="tl-period">21–25 September</div><div class="tl-focus display">UAT, pilot, rilis</div><p class="tl-output">Uji akhir tahun simulasi, scan nyata, backup/restore, dokumentasi operator.</p></div>
    </div>

    <div class="gate-row">
      <span class="gate">G1 — Data legacy · 21 Agu</span>
      <span class="gate">G2 — Kebijakan waktu · 21 Agu</span>
      <span class="gate">G3 — Tahun ajaran aktif · 21 Agu</span>
      <span class="gate">G4 — UAT · 18 Sep</span>
    </div>
  </div>
</section>

<section id="siap">
  <div class="wrap">
    <div class="sec-head"><span class="sec-num mono">10</span><h2 class="sec-title">Kriteria siap produksi</h2></div>
    <ul class="ready-list">
      <li>Tidak ada <code class="mono">class_id</code>/<code class="mono">kelas</code> yang dipakai sebagai sumber kebenaran kelas.</li>
      <li>Semua siswa aktif memiliki membership valid pada tahun ajaran aktif.</li>
      <li>Kasus naik, tinggal, lulus, pindah, dan keluar lulus pengujian.</li>
      <li>Alumni/pindahan tidak dapat scan dengan kartu lama.</li>
      <li>Laporan tahun sebelumnya tidak berubah setelah transisi akademik.</li>
      <li>Backup/restore dan rollback migrasi telah diuji.</li>
      <li>Endpoint admin terlindungi dan terminal RFID diautentikasi.</li>
      <li>Pemeriksaan syntax PHP lulus; feature test tersedia untuk dijalankan.</li>
    </ul>
  </div>
</section>

<footer>
  <div class="wrap footer-grid">
    <div class="foot-files">
      Prototype_Absensi_RFID_SMKN_1_Air_Naningan_V2.docx<br>
      UML_Absensi_RFID_SMKN_1_Air_Naningan_V2.docx<br>
      Baseline_Terstruktur_Sistem_Absensi_RFID_v4.docx<br>
      Analisis_Kode_dan_Timeline_Pengembangan_Absensi_RFID_v2.docx
    </div>
    <div class="foot-note">
      <span class="k">Catatan verifikasi</span>
      Pemeriksaan syntax PHP telah lulus. Feature test sudah ditambahkan, namun belum dieksekusi karena source awal tidak menyertakan <code class="mono">vendor/</code> Composer.
    </div>
  </div>
</footer>

</body>
</html>