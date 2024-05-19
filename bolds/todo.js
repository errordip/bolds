document.addEventListener('DOMContentLoaded', () => {
    const usernameSpan = document.getElementById('username');

    // Fetch the username from the server
    fetch('task.php?action=getUsername')
        .then(response => response.json())
        .then(data => {
            // Update the content of the <span> element with the username
            if (data.username) {
                usernameSpan.textContent = data.username;
            } else {
                console.error('Error fetching username:', data.error);
            }
        })
        .catch(error => console.error('Error fetching username:', error));

    const taskForm = document.getElementById('task-form');
    const taskInput = document.getElementById('new-task');
    const taskList = document.getElementById('task-list');

    // Function to fetch tasks from the server
    function fetchTasks() {
        fetch('task.php?action=fetch')
            .then(response => response.json())
            .then(tasks => {
                // Clear the existing task list
                taskList.innerHTML = '';
                // Iterate over each task and add it to the task list
                tasks.forEach(task => addTaskToList(task));
            })
            .catch(error => console.error('Error fetching tasks:', error));
    }

    // Function to add task to the task list
    function addTaskToList(task) {
        const li = document.createElement('li');
        const taskContent = document.createElement('span');
        taskContent.textContent = task.task;
        li.appendChild(taskContent);

        // Add Complete button
        const completeButton = document.createElement('button');
        completeButton.textContent = 'Complete';
        completeButton.addEventListener('click', () => {
            completeTask(task.id, !task.completed);
        });
        li.appendChild(completeButton);

        // Add Edit button
        const editButton = document.createElement('button');
        editButton.textContent = 'Edit';
        editButton.addEventListener('click', () => {
            editTask(task.id, task.task);
        });
        li.appendChild(editButton);

        // Add Delete button
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => {
            deleteTask(task.id);
        });
        li.appendChild(deleteButton);

        // Check if the task is completed and apply corresponding class
        if (task.completed) {
            li.classList.add('completed');
        }

        taskList.appendChild(li);
    }

    // Function to add new task
    taskForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const taskText = taskInput.value.trim();
        if (taskText !== '') {
            addTask(taskText);
            taskInput.value = '';
        }
    });

    function addTask(taskText) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('task', taskText);

        fetch('task.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Fetch tasks again to update the task list
                    fetchTasks();
                } else {
                    console.error('Error adding task:', data.error);
                }
            })
            .catch(error => console.error('Error adding task:', error));
    }

    // Function to mark task as completed
    function completeTask(taskId, completed) {
        const formData = new FormData();
        formData.append('action', 'complete');
        formData.append('id', taskId);

        fetch('task.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Fetch tasks again to update the task list
                    fetchTasks();
                } else {
                    console.error('Error completing task:', data.error);
                }
            })
            .catch(error => console.error('Error completing task:', error));
    }

    // Function to edit task
    function editTask(taskId, taskText) {
        const newTaskText = prompt('Edit task:', taskText);
        if (newTaskText !== null && newTaskText.trim() !== '') {
            const formData = new FormData();
            formData.append('action', 'edit');
            formData.append('id', taskId);
            formData.append('task', newTaskText.trim());

            fetch('task.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Fetch tasks again to update the task list
                        fetchTasks();
                    } else {
                        console.error('Error editing task:', data.error);
                    }
                })
                .catch(error => console.error('Error editing task:', error));
        }
    }

    // Function to delete task
    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', taskId);

            fetch('task.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Fetch tasks again to update the task list
                        fetchTasks();
                    } else {
                        console.error('Error deleting task:', data.error);
                    }
                })
                .catch(error => console.error('Error deleting task:', error));
        }
    }

    // Fetch tasks initially when the page loads
    fetchTasks();
});
