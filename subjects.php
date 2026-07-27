<?php
// 1. Connect to Database
require_once 'db.php';

// ============================================================================
// 2. BACKEND HANDLERS (Create, Update, Delete)
// ============================================================================

// Save or Update Subject
if (isset($_POST['save_subject'])) {
    $name = $_POST['subject_name'];
    $desc = $_POST['description'];
    $id   = $_POST['subject_id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE subjects SET subject_name=?, description=? WHERE subject_id=?");
        $stmt->execute([$name, $desc, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);
    }
    header("Location: subjects.php");
    exit;
}

// Delete Subject
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: subjects.php");
    exit;
}

// ============================================================================
// 3. FETCH DATA
// ============================================================================

// Fetch subject details if editing
$edit_subject = isset($_GET['edit']) ? $pdo->prepare("SELECT * FROM subjects WHERE subject_id = ?") : null;
if ($edit_subject) { 
    $edit_subject->execute([$_GET['edit']]); 
    $edit_subject = $edit_subject->fetch(); 
}

// Fetch all subjects
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY subject_id DESC")->fetchAll();

// ----------------------------------------------------------------------------
// 4. INCLUDE THE HEADER HERE (Navigation + Live Search Bar)
// ----------------------------------------------------------------------------
include 'header.php';
?>

<h2>Step 2: Subjects Management</h2>

<h3><?= $edit_subject ? 'Edit Subject' : 'Add New Subject' ?></h3>

<form action="subjects.php" method="POST">
    <?php if ($edit_subject): ?>
        <input type="hidden" name="subject_id" value="<?= $edit_subject['subject_id'] ?>">
    <?php endif; ?>

    <p>
        <label>Subject Name:</label>
        <input type="text" name="subject_name" value="<?= $edit_subject['subject_name'] ?? '' ?>" required>
    </p>

    <p>
        <label>Description:</label>
        <textarea name="description"><?= $edit_subject['description'] ?? '' ?></textarea>
    </p>

    <p>
        <button type="submit" name="save_subject"><?= $edit_subject ? 'Update Subject' : 'Add Subject' ?></button>
    </p>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subjects as $sub): ?>
            <tr>
                <td><?= $sub['subject_id'] ?></td>
                <td><?= htmlspecialchars($sub['subject_name']) ?></td>
                <td><?= htmlspecialchars($sub['description'] ?? 'None') ?></td>
                <td>
                    <a href="subjects.php?edit=<?= $sub['subject_id'] ?>">Edit</a> | 
                    <a href="subjects.php?delete=<?= $sub['subject_id'] ?>" class="btn-delete" onclick="return confirm('Delete this subject?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>