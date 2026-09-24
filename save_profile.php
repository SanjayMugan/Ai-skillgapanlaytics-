<?php
// 1. DATABASE CONNECTION
$host = "localhost";
$db   = "skillforge_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. FETCH USER DATA (Assuming ID 1 for the logged-in user)
    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE id = 1");
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fallback if database is empty
    if (!$userData) {
        $userData = [
            'first_name' => 'New', 'last_name' => 'User', 'email' => '', 
            'phone' => '', 'employee_id' => 'EMP-00000', 'joining_date' => '',
            'bio' => '', 'department' => '', 'job_role' => '', 
            'experience_years' => 0, 'reporting_manager' => '', 'status' => 'Active'
        ];
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>SkillForge — Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=Fraunces:ital,wght@0,300;0,700;1,300&display=swap" rel="stylesheet"/>
<style>
:root{
  --bg:#0a0c0f;--surface:#111318;--surface2:#181c23;
  --border:rgba(255,255,255,0.07);
  --accent:#e8ff47;--accent2:#00d4ff;--accent3:#ff6b35;
  --text:#f0f2f5;--muted:#6b7280;
  --danger:#ff4444;--success:#22c55e;--warn:#f59e0b;--ai:#a78bfa;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);font-family:'DM Mono',monospace;min-height:100vh;overflow-x:hidden;}
/* ... (Your existing CSS remains exactly the same) ... */
.shell{position:relative;z-index:1;display:grid;grid-template-columns:240px 1fr;min-height:100vh;}
.sidebar{background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:28px 0;position:sticky;top:0;height:100vh;overflow-y:auto;}
.logo{padding:0 24px 28px;border-bottom:1px solid var(--border);}
.logo-mark{display:flex;align-items:center;gap:10px;text-decoration:none;}
.logo-icon{width:36px;height:36px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.logo-text{font-family:'Syne',sans-serif;font-weight:800;font-size:18px;color:var(--text);letter-spacing:-.5px;}
.logo-sub{font-size:9px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
nav{padding:20px 0;flex:1;}
.nav-section{padding:0 16px 8px;}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;cursor:pointer;transition:all .2s;color:var(--muted);font-size:13px;font-family:'DM Mono',monospace;border:1px solid transparent;margin-bottom:2px;position:relative;}
.nav-item:hover{background:var(--surface2);color:var(--text);}
.nav-item.active{background:rgba(232,255,71,.08);color:var(--accent);border-color:rgba(232,255,71,.15);}
.main{display:flex;flex-direction:column;min-height:100vh;}
.topbar{background:rgba(10,12,15,.85);backdrop-filter:blur(20px);border-bottom:1px solid var(--border);padding:16px 32px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:10;}
.page-title{font-family:'Syne',sans-serif;font-weight:700;font-size:20px;letter-spacing:-.5px;}
.page-title span{color:var(--accent);}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:12px;}
.btn{padding:8px 16px;border-radius:8px;font-size:12px;font-family:'DM Mono',monospace;cursor:pointer;transition:all .2s;border:none;}
.btn-accent{background:var(--accent);color:#000;font-weight:500;}
.content{padding:32px;flex:1;display:flex;flex-direction:column;gap:24px;max-width:1000px;width:100%;}
.profile-hero{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;position:relative;}
.hero-banner{height:100px;background:linear-gradient(135deg,rgba(232,255,71,.06),rgba(0,212,255,.04),rgba(167,139,250,.06));}
.hero-body{padding:0 28px 24px;display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;}
.avatar-wrap{margin-top:-36px;position:relative;z-index:1;}
.avatar-lg{width:72px;height:72px;border-radius:16px;background:linear-gradient(135deg,var(--accent2),var(--accent));border:3px solid var(--bg);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#000;}
.hero-name{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;letter-spacing:-1px;margin-bottom:4px;}
.profile-tabs{display:flex;gap:0;border-bottom:1px solid var(--border);background:var(--surface);}
.ptab{padding:13px 20px;font-size:12px;color:var(--muted);cursor:pointer;border:none;background:none;border-bottom:2px solid transparent;}
.ptab.active{color:var(--accent);border-bottom-color:var(--accent);}
.card{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:20px;}
.card-head{padding:16px 22px 13px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;}
.card-body{padding:22px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group.full{grid-column:1/-1;}
.form-label{font-size:10px;text-transform:uppercase;color:var(--muted);}
.form-input, .form-select, .form-textarea{background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px;color:var(--text);font-family:inherit;}
.tab-screen{display:none;}
.tab-screen.active{display:block;}
</style>
</head>
<body>

<div class="shell">
  <aside class="sidebar">
    <div class="logo">
      <div class="logo-mark">
        <div class="logo-icon">SF</div>
        <div><div class="logo-text">SkillForge</div></div>
      </div>
    </div>
    <nav>
      <div class="nav-section"><div class="nav-item">Dashboard</div></div>
      <div class="nav-section"><div class="nav-item active">Profile</div></div>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="page-title">My <span>Profile</span></div>
      <div style="font-size:11px;color:var(--muted);margin-left:4px;">— <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></div>
      <div class="topbar-right">
        <button class="btn btn-accent" onclick="saveProfile()">Save Changes</button>
      </div>
    </div>

    <div class="content">
      <div class="profile-hero">
        <div class="hero-banner"></div>
        <div class="hero-body">
          <div class="avatar-wrap">
            <div class="avatar-lg">
                <?php echo strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1)); ?>
            </div>
          </div>
          <div class="hero-info">
            <div class="hero-name"><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></div>
            <div class="hero-role"><?php echo htmlspecialchars($userData['job_role']); ?></div>
          </div>
        </div>
      </div>

      <div class="profile-tabs">
        <button class="ptab active" onclick="switchTab('tab-info')">Personal Info</button>
      </div>

      <div class="tab-screen active" id="tab-info">
        <div class="card">
          <div class="card-head"><div class="card-title">Personal Information</div></div>
          <div class="card-body">
            <form id="profileForm">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">First Name</label>
                  <input class="form-input" name="first_name" type="text" value="<?php echo htmlspecialchars($userData['first_name']); ?>"/>
                </div>
                <div class="form-group">
                  <label class="form-label">Last Name</label>
                  <input class="form-input" name="last_name" type="text" value="<?php echo htmlspecialchars($userData['last_name']); ?>"/>
                </div>
                <div class="form-group">
                  <label class="form-label">Email Address</label>
                  <input class="form-input" name="email" type="email" value="<?php echo htmlspecialchars($userData['email']); ?>"/>
                </div>
                <div class="form-group">
                  <label class="form-label">Employee ID</label>
                  <input class="form-input" name="employee_id" type="text" value="<?php echo htmlspecialchars($userData['employee_id']); ?>" readonly style="opacity:0.5"/>
                </div>
                <div class="form-group full">
                  <label class="form-label">Bio / Summary</label>
                  <textarea class="form-textarea" name="bio"><?php echo htmlspecialchars($userData['bio']); ?></textarea>
                </div>
                
                <div class="form-group">
                  <label class="form-label">Department</label>
                  <select class="form-select" name="department">
                    <option <?php if($userData['department'] == 'SDV / OTA Systems') echo 'selected'; ?>>SDV / OTA Systems</option>
                    <option <?php if($userData['department'] == 'Battery Cell Assembly') echo 'selected'; ?>>Battery Cell Assembly</option>
                    <option <?php if($userData['department'] == 'Quality & Compliance') echo 'selected'; ?>>Quality & Compliance</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
function saveProfile() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    
    // Convert FormData to JSON
    const data = Object.fromEntries(formData.entries());

    // Basic visual feedback
    const btn = document.querySelector('.btn-accent');
    btn.innerText = "Saving...";

    fetch('save_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if(result.status === 'success') {
            alert('Profile updated successfully!');
            location.reload(); // Reload to see changes
        } else {
            alert('Error: ' + result.message);
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => btn.innerText = "Save Changes");
}

function switchTab(tabId) {
    document.querySelectorAll('.tab-screen').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
}
</script>

</body>
</html>