<div id="data">
    <div class="bigText">Achievements</div>
    <div class="br"></div>
    <ul class="list-group col-md-6">
        <?php
        foreach ($globalAch as $ach): {
            $ach = (array) $ach;
            ?>
            <li class="list-group-item">
                <span class="badge"><?php echo $ach['ach_count']; ?></span>
                <?php echo $ach['achievs_name']; ?>
            </li>
            <?php
        }
        endforeach;
        ?>
    </ul>
    <div class="medText">Your achievements</div>
    <?php
    if (isset($achievs) && $achievs != false) {
        ?>
        <table class="table table-striped">
            <tr><th>Group</th><th>Name</th><th>Desc</th><th>Got</th><th>Date</th></tr>
            <?php
            foreach ($achievs as $item):
                ?>
                <tr>
                    <td>
                        <?php echo $item['ach_gr']; ?>
                    </td>
                    <td>
                        <?php echo $item['ach_name']; ?>
                    </td>
                    <td>
                        <?php echo $item['ach_desc']; ?>
                    </td>
                    <td>
                        <?php echo $item['ach_checked']; ?>
                    </td>
                    <th>
                        <?php echo ($item['ach_checked'] == 'true') ? date('Y/m/d', strtotime($item['ach_got'])) : ''; ?>
                    </th>
                <tr>
                    <?php
                endforeach;
                ?> 
        </table> <?php
    } else {
        echo "<p style='clear: both;'>Sorry, we can't take your achievements</p>";
    }
    ?>
</div>
</body>
</html>