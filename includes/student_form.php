<?php
$opts = studentFieldOptions();
$v = $values ?? [];
$errors = $errors ?? [];
$id_locked = $id_locked ?? false;
$student_id_display = $student_id_display ?? '';
$submit_label = $submit_label ?? 'Save Student';
$cancel_href = $cancel_href ?? 'students.php';

if (!function_exists('sf_val')) {
  function sf_val($v, $k) { return htmlspecialchars((string)($v[$k] ?? '')); }
  function sf_sel($v, $k, $opt) { return (($v[$k] ?? '') === $opt) ? 'selected' : ''; }
  function sf_err($errors, $k) {
      if (empty($errors[$k])) return '';
      return '<div class="field-error">' . htmlspecialchars($errors[$k]) . '</div>';
  }
  function sf_cls($errors, $k) { return empty($errors[$k]) ? '' : ' is-invalid'; }
}
?>
<form method="POST" action="" class="student-form" novalidate>
  <div class="form-banner">
    <div>
      <h1><?= htmlspecialchars($form_title ?? 'Add Student') ?></h1>
      <p>Fill in student details. Fields marked <span class="req">*</span> are required.</p>
    </div>
    <div class="id-chip">ID: <strong><?= htmlspecialchars($student_id_display) ?></strong></div>
  </div>

  <section class="form-section">
    <h3>Personal Information</h3>
    <div class="form-grid">
      <div class="form-group">
        <label>Full Name <span class="req">*</span></label>
        <input type="text" name="full_name" placeholder="Enter full name" value="<?= sf_val($v,'full_name') ?>" class="<?= sf_cls($errors,'full_name') ?>" required>
        <?= sf_err($errors,'full_name') ?>
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="">Select gender</option>
          <?php foreach ($opts['genders'] as $g): ?>
            <option value="<?= $g ?>" <?= sf_sel($v,'gender',$g) ?>><?= $g ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= sf_val($v,'date_of_birth') ?>">
      </div>
    </div>
  </section>

  <section class="form-section">
    <h3>Academic Information</h3>
    <div class="form-grid">
      <div class="form-group">
        <label>Program <span class="req">*</span></label>
        <select name="program" class="<?= sf_cls($errors,'program') ?>" required>
          <option value="">Select program</option>
          <?php foreach ($opts['programs'] as $p): ?>
            <option value="<?= $p ?>" <?= sf_sel($v,'program',$p) ?>><?= $p ?></option>
          <?php endforeach; ?>
        </select>
        <?= sf_err($errors,'program') ?>
      </div>
      <div class="form-group">
        <label>Year <span class="req">*</span></label>
        <select name="year" class="<?= sf_cls($errors,'year') ?>" required>
          <option value="">Select year</option>
          <?php foreach ($opts['years'] as $y): ?>
            <option value="<?= $y ?>" <?= sf_sel($v,'year',$y) ?>><?= $y ?></option>
          <?php endforeach; ?>
        </select>
        <?= sf_err($errors,'year') ?>
      </div>
      <div class="form-group">
        <label>Section</label>
        <select name="section">
          <option value="">Select section</option>
          <?php foreach ($opts['sections'] as $s): ?>
            <option value="<?= $s ?>" <?= sf_sel($v,'section',$s) ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <?php foreach ($opts['statuses'] as $st): ?>
            <option value="<?= $st ?>" <?= sf_sel($v,'status',$st) ?: ($st==='Active' && empty($v['status']) ? 'selected' : '') ?>><?= $st ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </section>

  <section class="form-section">
    <h3>Contact Information</h3>
    <div class="form-grid">
      <div class="form-group">
        <label>Email <span class="req">*</span></label>
        <input type="email" name="email" placeholder="name@example.com" value="<?= sf_val($v,'email') ?>" class="<?= sf_cls($errors,'email') ?>" required>
        <?= sf_err($errors,'email') ?>
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="tel" name="phone" placeholder="98XXXXXXXX" value="<?= sf_val($v,'phone') ?>" class="<?= sf_cls($errors,'phone') ?>">
        <?= sf_err($errors,'phone') ?>
      </div>
      <div class="form-group">
        <label>Guardian Name</label>
        <input type="text" name="guardian_name" placeholder="Parent / guardian" value="<?= sf_val($v,'guardian_name') ?>">
      </div>
      <div class="form-group">
        <label>Guardian Phone</label>
        <input type="tel" name="guardian_phone" placeholder="98XXXXXXXX" value="<?= sf_val($v,'guardian_phone') ?>">
      </div>
      <div class="form-group span-all">
        <label>Address</label>
        <input type="text" name="address" placeholder="Street, city" value="<?= sf_val($v,'address') ?>">
      </div>
      <div class="form-group span-all">
        <label>Notes</label>
        <textarea name="notes" rows="3" placeholder="Optional remarks"><?= sf_val($v,'notes') ?></textarea>
      </div>
    </div>
  </section>

  <div class="form-actions">
    <button type="submit" class="btn-primary btn-sm"><?= htmlspecialchars($submit_label) ?></button>
    <a href="<?= htmlspecialchars($cancel_href) ?>" class="btn-secondary">Cancel</a>
  </div>
</form>
