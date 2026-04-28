<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $min = $_POST['min_temp'];
    $max = $_POST['max_temp'];
    $humidity = $_POST['humidity'];

    mysqli_query($conn, "INSERT INTO vegetables (name, min_temp, max_temp, humidity)
                         VALUES ('$name','$min','$max','$humidity')");
    $message = "Vegetable profile added.";
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM vegetables WHERE id=$id");
    $message = "Vegetable profile deleted.";
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $min = $_POST['min_temp'];
    $max = $_POST['max_temp'];
    $humidity = $_POST['humidity'];

    mysqli_query($conn, "UPDATE vegetables
        SET name='$name', min_temp='$min', max_temp='$max', humidity='$humidity'
        WHERE id=$id");
    $message = "Vegetable profile updated.";
}

$result = mysqli_query($conn, "SELECT * FROM vegetables ORDER BY name ASC");

$page_title = "Vegetables";
$active_page = "vegetables";
include("header.php");
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Produce profiles</p>
        <h2>Manage safe ranges for every vegetable.</h2>
        <p>Set ideal minimum and maximum temperatures plus humidity targets so each new reading can be classified instantly.</p>
    </div>
    <div class="actions">
        <a class="btn btn-muted" href="temperature.php">Enter Reading</a>
    </div>
</section>

<?php if ($message !== "") { ?>
    <div class="message success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>

<section class="grid-two">
    <article class="form-card">
        <h3>Add Vegetable</h3>
        <form method="POST">
            <div class="form-grid">
                <div class="field">
                    <label for="name">Vegetable name</label>
                    <input id="name" type="text" name="name" placeholder="Example: Tomato" required>
                </div>

                <div class="field">
                    <label for="humidity">Humidity target</label>
                    <input id="humidity" type="number" name="humidity" placeholder="Example: 85" required>
                </div>

                <div class="field">
                    <label for="min_temp">Minimum temperature</label>
                    <input id="min_temp" type="number" step="0.1" name="min_temp" placeholder="Example: 4" required>
                </div>

                <div class="field">
                    <label for="max_temp">Maximum temperature</label>
                    <input id="max_temp" type="number" step="0.1" name="max_temp" placeholder="Example: 10" required>
                </div>
            </div>

            <button style="margin-top:18px;" name="add">Add Vegetable</button>
        </form>
    </article>

    <article class="panel">
        <div class="panel-header">
            <h3>Configured Profiles</h3>
            <span class="badge badge-active"><?php echo $result ? mysqli_num_rows($result) : 0; ?> items</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Min Temp</th>
                        <th>Max Temp</th>
                        <th>Humidity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <form method="POST">
                                    <td>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" name="min_temp" value="<?php echo htmlspecialchars($row['min_temp'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" name="max_temp" value="<?php echo htmlspecialchars($row['max_temp'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="humidity" value="<?php echo htmlspecialchars($row['humidity'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <button name="update">Update</button>
                                        <a class="btn btn-danger" href="?delete=<?php echo (int) $row['id']; ?>" onclick="return confirm('Delete this vegetable profile?')">Delete</a>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="5">No vegetable profiles have been added yet.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<?php include("footer.php"); ?>
