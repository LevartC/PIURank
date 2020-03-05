<?php 
    $sql = "SELECT * FROM barunai_vod";

    $query = $this->db->query($sql);
    foreach ($query->result() as $row) {
        echo $row->vod_num;
        echo "<br/>";
        echo $row->vod_category;
        echo "<br/>";
        echo $row->vod_addr;
        echo "<br/>";
        echo $row->vod_subject;
        echo "<br/>";
        echo $row->vod_content;
        echo "<br/>";
        echo $row->vod_uptime;
        echo "<br/>";
        echo $row->vod_visible;
        echo "<br/>";
        echo "<br/>";
    }

?>