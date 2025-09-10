<?php
require_once 'config/config.php';
require_once 'classes/Note.php';
require_once 'classes/User.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();
$note = new Note($db);
$user = new User($db);

// Get user info
$user->getUserById($_SESSION['user_id']);

// Get user notes
$notes = $note->getUserNotes($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h2><?php echo APP_NAME; ?></h2>
            </div>
            <div class="nav-menu">
                <div class="user-menu">
                    <div class="user-avatar" onclick="toggleUserMenu()">
                        <img src="<?php echo UPLOAD_PATH . ($user->profile_image ?: 'default-avatar.png'); ?>" alt="Profile">
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="dashboard-header">
            <h1>My Notes</h1>
            <button class="btn btn-primary" onclick="openNoteModal()">
                <i class="fas fa-plus"></i> Add New Note
            </button>
        </div>

        <div class="notes-grid" id="notesGrid">
            <?php if (empty($notes)): ?>
                <div class="empty-state">
                    <i class="fas fa-sticky-note"></i>
                    <h3>No notes yet</h3>
                    <p>Create your first note to get started!</p>
                    <button class="btn btn-primary" onclick="openNoteModal()">
                        <i class="fas fa-plus"></i> Create Note
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($notes as $noteItem): ?>
                    <div class="note-card" style="background-color: <?php echo htmlspecialchars($noteItem['color']); ?>" data-note-id="<?php echo $noteItem['id']; ?>">
                        <div class="note-header">
                            <h3><?php echo htmlspecialchars($noteItem['title']); ?></h3>
                            <div class="note-actions">
                                <button onclick="openColorPicker(<?php echo $noteItem['id']; ?>)" class="btn-icon" title="Change Color">
                                    <i class="fas fa-palette"></i>
                                </button>
                                <button onclick="editNote(<?php echo $noteItem['id']; ?>)" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteNote(<?php echo $noteItem['id']; ?>)" class="btn-icon btn-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="note-content">
                            <p><?php echo nl2br(htmlspecialchars($noteItem['content'])); ?></p>
                        </div>
                        <div class="note-footer">
                            <small>Updated: <?php echo date('M j, Y g:i A', strtotime($noteItem['updated_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Note Modal -->
    <div class="modal" id="noteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Note</h3>
                <button class="modal-close" onclick="closeNoteModal()">&times;</button>
            </div>
            <form id="noteForm">
                <input type="hidden" id="noteId" value="">
                <div class="form-group">
                    <label for="noteTitle">Title</label>
                    <input type="text" id="noteTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="noteContent">Content</label>
                    <textarea id="noteContent" name="content" rows="8" required></textarea>
                </div>
                <div class="form-group">
                    <label for="noteColor">Color</label>
                    <div class="color-picker">
                        <input type="color" id="noteColor" name="color" value="#ffffff">
                        <div class="color-presets">
                            <div class="color-preset" style="background: #ffffff" onclick="setColor('#ffffff')"></div>
                            <div class="color-preset" style="background: #ffe6e6" onclick="setColor('#ffe6e6')"></div>
                            <div class="color-preset" style="background: #e6f3ff" onclick="setColor('#e6f3ff')"></div>
                            <div class="color-preset" style="background: #e6ffe6" onclick="setColor('#e6ffe6')"></div>
                            <div class="color-preset" style="background: #fff9e6" onclick="setColor('#fff9e6')"></div>
                            <div class="color-preset" style="background: #f3e6ff" onclick="setColor('#f3e6ff')"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeNoteModal()">Cancel</button>
                <button type="submit" form="noteForm" class="btn btn-primary">Save Note</button>
            </div>
        </div>
    </div>

    <!-- Color Picker Modal -->
    <div class="modal" id="colorModal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>Change Note Color</h3>
                <button class="modal-close" onclick="closeColorModal()">&times;</button>
            </div>
            <div class="color-grid">
                <div class="color-option" style="background: #ffffff" onclick="changeNoteColor('#ffffff')"></div>
                <div class="color-option" style="background: #ffe6e6" onclick="changeNoteColor('#ffe6e6')"></div>
                <div class="color-option" style="background: #e6f3ff" onclick="changeNoteColor('#e6f3ff')"></div>
                <div class="color-option" style="background: #e6ffe6" onclick="changeNoteColor('#e6ffe6')"></div>
                <div class="color-option" style="background: #fff9e6" onclick="changeNoteColor('#fff9e6')"></div>
                <div class="color-option" style="background: #f3e6ff" onclick="changeNoteColor('#f3e6ff')"></div>
                <div class="color-option" style="background: #ffcccc" onclick="changeNoteColor('#ffcccc')"></div>
                <div class="color-option" style="background: #ccddff" onclick="changeNoteColor('#ccddff')"></div>
                <div class="color-option" style="background: #ccffcc" onclick="changeNoteColor('#ccffcc')"></div>
                <div class="color-option" style="background: #fff5cc" onclick="changeNoteColor('#fff5cc')"></div>
                <div class="color-option" style="background: #e6ccff" onclick="changeNoteColor('#e6ccff')"></div>
                <div class="color-option" style="background: #ffd9cc" onclick="changeNoteColor('#ffd9cc')"></div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-danger"></i> Delete Note</h3>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="delete-modal-body">
                <p>Are you sure you want to delete this note?</p>
                <p class="delete-note-title"><strong id="deleteNoteTitle"></strong></p>
                <p class="delete-warning">This action cannot be undone.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete Note
                </button>
            </div>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
