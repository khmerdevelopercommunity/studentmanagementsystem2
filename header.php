<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Primary School Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Primary School Management System (Grades 1-6)</h1>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="teachers.php">1. Teachers</a>
    <a href="subjects.php">2. Subjects</a>
    <a href="classes.php">3. Classes</a>
    <a href="students.php">4. Students</a>
    <a href="enroll.php">5. Enrollments</a>

    <div class="search-container">
        <form action="search.php" method="GET" class="search-form" style="margin: 0; padding: 0; background: none; border: none; box-shadow: none;">
            <input type="text" id="live-search-input" name="q" placeholder="Search..." autocomplete="off" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" required>
            <button type="submit">Search</button>
        </form>
        <div id="live-search-results"></div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live-search-input');
    const resultsBox = document.getElementById('live-search-results');

    if (!searchInput || !resultsBox) return;

    function highlightText(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length === 0) {
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
            return;
        }

        fetch('ajax_search.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                let html = '';
                let totalFound = 0;

                // 1. Teachers
                if (data.teachers && data.teachers.length > 0) {
                    html += '<div class="search-group-title">Teachers</div>';
                    data.teachers.forEach(item => {
                        totalFound++;
                        html += `<a class="search-item" href="teachers.php?edit=${item.teacher_id}">
                                    <div>${highlightText(item.title, query)}</div>
                                    <div class="search-item-sub">${item.sub || ''}</div>
                                 </a>`;
                    });
                }

                // 2. Subjects
                if (data.subjects && data.subjects.length > 0) {
                    html += '<div class="search-group-title">Subjects</div>';
                    data.subjects.forEach(item => {
                        totalFound++;
                        html += `<a class="search-item" href="subjects.php?edit=${item.subject_id}">
                                    <div>${highlightText(item.title, query)}</div>
                                 </a>`;
                    });
                }

                // 3. Classes
                if (data.classes && data.classes.length > 0) {
                    html += '<div class="search-group-title">Classes</div>';
                    data.classes.forEach(item => {
                        totalFound++;
                        html += `<a class="search-item" href="classes.php?edit=${item.class_id}">
                                    <div>${highlightText(item.title, query)}</div>
                                    <div class="search-item-sub">${item.sub || ''}</div>
                                 </a>`;
                    });
                }

                // 4. Students
                if (data.students && data.students.length > 0) {
                    html += '<div class="search-group-title">Students</div>';
                    data.students.forEach(item => {
                        totalFound++;
                        html += `<a class="search-item" href="students.php?edit=${item.student_id}">
                                    <div>${highlightText(item.title, query)}</div>
                                 </a>`;
                    });
                }

                if (totalFound > 0) {
                    resultsBox.innerHTML = html;
                    resultsBox.style.display = 'block';
                } else {
                    resultsBox.innerHTML = '<div class="search-item" style="color:#a0aec0;">No matches found</div>';
                    resultsBox.style.display = 'block';
                }
            });
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });
});
</script>