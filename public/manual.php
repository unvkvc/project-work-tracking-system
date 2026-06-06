<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help & Documentation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f4f6f9; }
        .manual-card {
            border: 0;
            border-radius: 18px;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                <i class="bi bi-question-circle"></i> Help & Documentation
            </h2>
            <p class="text-muted mb-0">
                This guide explains how to use the main features of the Project Work Tracking System.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-outline-secondary rounded-3">
            Back to Dashboard
        </a>
    </div>

    <div class="card manual-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4>Dashboard</h4>
            <p>The dashboard is the main starting point of the system. It provides quick access to projects, tasks, time entries, and user management.</p>
        </div>
    </div>

    <div class="card manual-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4>Projects</h4>
            <p>Projects contain general project information such as name, description, dates, manager, status, and progress.</p>
            <ul>
                <li>Administrators and project managers can create and edit projects.</li>
                <li>Project progress is calculated from completed tasks.</li>
                <li>Logged work hours are displayed to support progress tracking.</li>
            </ul>
        </div>
    </div>

    <div class="card manual-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4>Tasks</h4>
            <p>Tasks are connected to projects and assigned to employees.</p>
            <ul>
                <li>Project managers can create tasks and assign them to employees.</li>
                <li>Employees can update task status and add descriptions of completed work.</li>
                <li>Task status is used when calculating project progress.</li>
            </ul>
        </div>
    </div>

    <div class="card manual-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4>Time Entries</h4>
            <p>Time entries are used to record work performed on a specific task and date.</p>
            <ul>
                <li>Select the related task.</li>
                <li>Choose the work date.</li>
                <li>Enter hours and minutes worked.</li>
                <li>Add an optional work description.</li>
            </ul>
        </div>
    </div>

    <div class="card manual-card shadow-sm mb-4">
        <div class="card-body p-4">

            <h4>Project Progress</h4>

            <p>
                The system provides an overview of project progress to help managers and employees monitor project completion.
            </p>

            <ul>
                <li>Progress is calculated based on completed tasks within a project.</li>
                <li>The total number of tasks and completed tasks are displayed.</li>
                <li>Recorded work hours contribute to project monitoring and reporting.</li>
                <li>Project managers can use this information to identify delays and track project status.</li>
            </ul>

        </div>
    </div>

    <div class="card manual-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4>User Roles</h4>
            <ul>
                <li><strong>Administrator:</strong> manages users, projects, tasks, time entries, and project progress.</li>
                <li><strong>Project Manager:</strong> manages projects, creates tasks, records work hours, and monitors progress.</li>
                <li><strong>Employee:</strong> views assigned tasks, updates task status, records work hours, and views progress.</li>
            </ul>
        </div>
    </div>

</div>

</body>
</html>