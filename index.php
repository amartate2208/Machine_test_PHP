<?php
include_once 'db_connect.php';
include_once 'member.class.php';

$database = new Database();
$db = $database->getConnection();
$member = new Member($db);
$members = $member->readRecursive();

function displayTree($members) {
    echo "<ul>";
    foreach ($members as $member) {
        echo "<li data-id='" . $member['Id'] . "'>" . $member['Name'];
        if (!empty($member['children'])) {
            displayTree($member['children']);
        }
        echo "</li>";
    }
    echo "</ul>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Members</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Members</h1>
    <?php displayTree($members); ?>
    <button id="addMemberBtn">Add Member</button><br>

    <div id="addMemberModal" style="display:none;">
        <form id="addMemberForm"><br>
            <label for="parent">Parent:</label>
            <select id="parent" name="parent">
                <option value="0">None</option>
                <?php
                foreach ($members as $member) {
                    echo "<option value='" . $member['Id'] . "'>" . $member['Name'] . "</option>";
                }
                ?>
            </select>
            <br><br>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
            <br><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#addMemberBtn').click(function() {
                $('#addMemberModal').show();
            });

            $('#addMemberForm').submit(function(event) {
                event.preventDefault();
                var parent = $('#parent').val();
                var name = $('#name').val();

                if (name === '') {
                    alert('Name cannot be empty');
                    return;
                }

                $.ajax({
                    url: 'add_member.php',
                    type: 'POST',
                    data: { parent: parent, name: name },
                    success: function(response) {
                        var newMember = JSON.parse(response);
                        var parentElement = (newMember.parentId == 0) ? $('ul:first') : $('li[data-id="' + newMember.parentId + '"] > ul');
                        if (parentElement.length === 0) {
                            $('li[data-id="' + newMember.parentId + '"]').append('<ul></ul>');
                            parentElement = $('li[data-id="' + newMember.parentId + '"] > ul');
                        }
                        parentElement.append('<li data-id="' + newMember.id + '">' + newMember.name + '</li>');
                        $('#addMemberModal').hide();
                        $('#addMemberForm')[0].reset();
                    },
                    error: function(xhr, status, error) {
                        console.log('Error: ' + error);
                        console.log('Status: ' + status);
                        console.dir(xhr);
                    }
                });
            });
        });
    </script>
</body>
</html>
