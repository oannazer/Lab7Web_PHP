<?php
declare(strict_types=1);

// Pemrosesan data dilakukan sebelum HTML agar alur validasi terpisah dari tampilan.
$data = null;
$errors = [];
$input = [
    'nama' => '',
    'nim' => '',
    'prodi' => '',
    'semester' => '',
    'email' => '',
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($input) as $field) {
        $input[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if ($input['nama'] === '') {
        $errors[] = 'Nama wajib diisi.';
    }
    if ($input['nim'] === '') {
        $errors[] = 'NIM wajib diisi.';
    } elseif (!preg_match('/^[0-9]{9}$/', $input['nim'])) {
        $errors[] = 'NIM harus terdiri dari 9 angka.';
    }
    if ($input['prodi'] === '') {
        $errors[] = 'Program studi wajib diisi.';
    }
    if (!ctype_digit($input['semester']) || (int) $input['semester'] < 1 || (int) $input['semester'] > 14) {
        $errors[] = 'Semester harus berupa bilangan bulat dari 1 sampai 14.';
    }
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Masukkan alamat email dengan format yang valid.';
    }

    if ($errors === []) {
        $data = $input;
        $data['semester'] = (string) (int) $input['semester'];
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Data Mahasiswa</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            color: #202b3c;
            background: #f2f5fa;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
        }

        main {
            width: min(100%, 680px);
            margin: 0 auto;
        }

        .card {
            padding: clamp(20px, 5vw, 36px);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 12px 36px #1e3a5f12;
        }

        h1 {
            margin: 0 0 8px;
            color: #173f75;
            font-size: clamp(1.5rem, 4vw, 2rem);
        }

        .intro {
            margin: 0 0 24px;
            color: #64748b;
            line-height: 1.6;
        }

        .field {
            margin: 0 0 17px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 700;
        }

        input {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #b9c5d5;
            border-radius: 8px;
            font: inherit;
        }

        input:focus {
            outline: 3px solid #dbeafe;
            border-color: #3478c8;
        }

        .hint {
            margin: 5px 0 0;
            color: #64748b;
            font-size: .875rem;
        }

        button {
            width: 100%;
            min-height: 46px;
            border: 0;
            border-radius: 8px;
            background: #2868ad;
            color: white;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #1e548e;
        }

        .errors {
            margin: 0 0 22px;
            padding: 14px 18px;
            border-left: 4px solid #c0392b;
            border-radius: 6px;
            background: #fff0ee;
            color: #8e251b;
        }

        .errors h2 {
            margin: 0 0 8px;
            font-size: 1rem;
        }

        .errors ul {
            margin: 0;
            padding-left: 20px;
            line-height: 1.6;
        }

        .result {
            margin-top: 24px;
            padding: 20px;
            border-left: 4px solid #2e9e57;
            border-radius: 8px;
            background: #eaf7ef;
        }

        .result h2 {
            margin: 0 0 14px;
            color: #14532d;
            font-size: 1.2rem;
        }

        dl {
            display: grid;
            grid-template-columns: minmax(110px, 140px) 1fr;
            gap: 10px 16px;
            margin: 0;
        }

        dt {
            color: #526274;
            font-weight: 700;
        }

        dd {
            margin: 0;
            overflow-wrap: anywhere;
        }

        @media (max-width: 440px) {
            body {
                padding: 16px 10px;
            }

            dl {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            dd {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <main>
        <section class="card" aria-labelledby="judul">
            <h1 id="judul">Formulir Data Mahasiswa</h1>
            <p class="intro">Isi data berikut untuk melihat ringkasan. Semua kolom wajib diisi.</p>

                <?php if ($errors !== []): ?>
                <section class="errors" role="alert" aria-labelledby="judul-error">
                    <h2 id="judul-error">Data belum dapat diproses</h2>
                    <ul>
                            <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>

            <form method="POST" action="">
                <div class="field">
                    <label for="nama">Nama lengkap</label>
                    <input id="nama" name="nama" type="text" maxlength="100" autocomplete="name"
                        value="<?= e($input['nama']) ?>" required>
                </div>
                <div class="field">
                    <label for="nim">NIM</label>
                    <input id="nim" name="nim" type="text" inputmode="numeric" pattern="[0-9]{9}" maxlength="9"
                        value="<?= e($input['nim']) ?>" aria-describedby="nim-hint" required>
                    <p class="hint" id="nim-hint">Masukkan 9 angka.</p>
                </div>
                <div class="field">
                    <label for="prodi">Program studi</label>
                    <input id="prodi" name="prodi" type="text" maxlength="100" value="<?= e($input['prodi']) ?>"
                        required>
                </div>
                <div class="field">
                    <label for="semester">Semester</label>
                    <input id="semester" name="semester" type="number" min="1" max="14" step="1"
                        value="<?= e($input['semester']) ?>" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" maxlength="254" autocomplete="email"
                        value="<?= e($input['email']) ?>" required>
                </div>
                <button type="submit">Kirim Data</button>
            </form>

                <?php if ($data !== null): ?>
                <section class="result" aria-labelledby="judul-ringkasan" aria-live="polite">
                    <h2 id="judul-ringkasan">Ringkasan Data Mahasiswa</h2>
                    <dl>
                        <dt>Nama</dt>
                        <dd><?= e($data['nama']) ?></dd>
                        <dt>NIM</dt>
                        <dd><?= e($data['nim']) ?></dd>
                        <dt>Program studi</dt>
                        <dd><?= e($data['prodi']) ?></dd>
                        <dt>Semester</dt>
                        <dd><?= e($data['semester']) ?></dd>
                        <dt>Email</dt>
                        <dd><?= e($data['email']) ?></dd>
                    </dl>
                </section>
                <?php endif; ?>
        </section>
    </main>
</body>

</html>