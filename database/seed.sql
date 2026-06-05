USE project_work_tracking;

-- ROLES
INSERT INTO roles (id, role_name, description) VALUES
(1, 'Administrator', 'System administrator'),
(2, 'Project Manager', 'Project manager'),
(3, 'Employee', 'Employee');

-- USERS
INSERT INTO users (id, first_name, last_name, email, password, role_id) VALUES
(1, 'Admin', 'User', 'admin@example.com', '$2y$10$if4xsY5.hEsVYLrIHGhSR.yYYNa0OMWw/ZV4yzCOP6.TC/1aK1wPu', 1),
(2, 'Maja', 'Manager', 'manager@example.com', '$2y$10$if4xsY5.hEsVYLrIHGhSR.yYYNa0OMWw/ZV4yzCOP6.TC/1aK1wPu', 2),
(3, 'John', 'Employee', 'employee@example.com', '$2y$10$REPLACE_HASH_EMPLOYEE', 3);

-- PROJECTS
INSERT INTO projects (id, name, description, start_date, end_date, status, manager_id) VALUES
(1, 'Website Redesign', 'Redesign of the company website', '2026-05-01', '2026-07-31', 'active', 2),
(2, 'Mobile Application', 'Development of a mobile application', '2026-06-01', '2026-09-30', 'active', 2);

-- TASKS
INSERT INTO tasks (id, name, description, status, deadline, project_id, assigned_user_id) VALUES
(1, 'Design Homepage', 'Create homepage wireframes and design', 'Completed', '2026-05-15', 1, 3),
(2, 'Implement Login', 'Develop user authentication functionality', 'In Progress', '2026-06-15', 1, 3),
(3, 'Create Dashboard', 'Develop dashboard overview page', 'Pending', '2026-06-30', 2, 3);

-- TIME ENTRIES
INSERT INTO time_entries (id, user_id, task_id, work_date, hours_worked, description) VALUES
(1, 3, 1, '2026-05-10', 4.00, 'Homepage design implementation'),
(2, 3, 2, '2026-06-05', 2.50, 'Login form development'),
(3, 3, 2, '2026-06-06', 3.00, 'Authentication testing');