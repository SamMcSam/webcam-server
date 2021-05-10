<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        shell_exec("./make_screenshot.sh");
    }

    // get all files
    $filenames = [];
    $files = [];
    if ($handle = opendir('screenshots/')) {
        while (false !== ($entry = readdir($handle))) {

            if ($entry != "." && $entry != "..") {
                $filenames[] = $entry;
            }
        }
        closedir($handle);
    }
    // ordered...
    sort($filenames);
    foreach ($filenames as $filename) {
        $date = new \DateTime(substr($filename, 0, 12));
        if (!array_key_exists($date->format("Y-m-d"), $files)) {
            $files[$date->format("Y-m-d")] = [];
        }
        $files[$date->format("Y-m-d")][] = [
            "file" => $filename,
            "date" => $date
        ];
    }

    $first = new \DateTime();
    if (count($files) > 0) {
        $first = $files[array_keys($files)[0]][0]["date"];
    }
    //$first = new \DateTime("2020-1-1");

    // make list of dates
    $list = array();
    $period = new \DatePeriod($first, (new \DateInterval('P1D')), new \DateTime("+23 hour"));
    foreach ($period as $date) {
        $key = $date->format("Y-m-d");
        if (array_key_exists($key, $files)) {
            $list = array_merge($list, $files[$key]);
        } else {
            $list[] = [
                "date" => $date
            ];
        }
    }
?>
<html>
    <head>
        <style>
            .scrollTable {
                overflow-x: auto;
                overflow-y: hidden;
            }
            table, th, td {
                border: 1px solid black;
            }
            td {
                padding: 50px 10px 50px 10px;
            }
            td div {
                white-space: nowrap;
                -webkit-transform: rotate(-60deg);
                -moz-transform: rotate(-60deg);
            }
            td:not(.has-picture) {
                background: grey;
            }
            .has-picture {
                cursor: pointer;
            }
            .isOn {
                background: orange;
            }

            .timelapseControls {
                margin: 10px;
            }
            .timelapseControls button {
                padding: 10px 30px 10px 30px;
            }
        </style>
    </head>
	<body>

		<h1>Webcam</h1>

		<img id="now" src="/webcam">

		<h2>Timelapse</h2>

        <div class="timelapseControls">
            <form method="post">
                <button id="first" type="button">↞</button>
                <button id="previous" type="button">←</button>
                <button id="take" type="submit" style="display: none;">📷</button>
                <button id="play" type="button">▶️</button>
                <button id="next" type="button">→</button>
                <button id="last" type="button">↠</button>
            </form>
        </div>

		<img id="timelapse" src="screenshots/<?php echo end($list)["file"];?>" style="height: auto;width: 600px;">

        <div class="scrollTable">
            <table>
                <tr>
                    <?php
                        foreach ($list as $i => $item) {
                            if ($item["file"]) {
                                echo "<td class='has-picture" . ($i == count($list) - 1 ? " isOn" : "") . "' data-file='" . $item["file"] . "'><div>" . $item["date"]->format("Y-m-d H:i") . "</div></td>";
                            } else {
                                echo "<td><div>" . $item["date"]->format("Y-m-d") . "</div></td>";
                            }
                        }
                    ?>
                </tr>
            </table>
        </div>

		<script type="text/javascript">
            var elements = document.getElementsByClassName("has-picture");
            for (var i = 0; i < elements.length; i++) {
                elements[i].addEventListener('click', function () {
                    var that = this;
                    document.getElementById("timelapse").src = "screenshots/"+this.getAttribute('data-file');
                    document.getElementById("timelapse").addEventListener('load', function loadImage() {
                        this.removeEventListener('load', loadImage);
                        that.classList.add("isOn");
                    });
                    var tds = document.getElementsByTagName("td")
                    for (var j = 0; j < tds.length; j++) {
                        tds[j].classList.remove("isOn");
                    }
                }, false);
            }

            var isOn = false;
            document.getElementById("play").addEventListener("click", function () {
                isOn = !isOn;
                if (isOn) {
                    this.textContent = "⏸";
                } else {
                    this.textContent = "▶️";
                }
            });
            setInterval(function () {
                if (isOn) {
                    document.getElementById("next").click();
                }
            },1000);

            document.getElementById("first").addEventListener("click", function () {
                var elements = document.getElementsByClassName("has-picture");
                elements[0].click();
            });
            function playNext(next) {
                var elements = document.getElementsByClassName("has-picture");
                for (var i = 0; i < elements.length; i++) {
                    if (elements[i].classList.contains("isOn")) {
                        break;
                    }
                }

                if (next) {
                    i++;
                } else {
                    i--;
                }

                if (i >= elements.length) {
                    i = 0;
                }
                if (i < 0) {
                    i = elements.length-1;
                }
                elements[i].click();
            }
            document.getElementById("previous").addEventListener("click", function () {
                playNext(false);
            });
            document.getElementById("next").addEventListener("click", function () {
                playNext(true);
            });
            document.getElementById("last").addEventListener("click", function () {
                var elements = document.getElementsByClassName("has-picture");
                elements[elements.length-1].click();
            });
        </script>
    </body>
</html>