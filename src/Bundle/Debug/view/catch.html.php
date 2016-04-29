<?php
/** @var string $message **/
/** @var string $file **/
/** @var string $line **/
/** @var string $code **/
/** @var array $trace **/
?>
<!DOCTYPE html>
<html>
<head>
    <title>Exception detected</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, Geneva, sans-serif;
            color: #4C4C4C;
        }
        .exception {
            padding: 50px 100px;
        }
        table {
            border-collapse: collapse;
            font-size: 1.5em;
        }
        table tr {
            border-bottom: 1px solid #dcdcdc;
        }
        table tr td {
            padding: 10px;
        }
        table tr td:first-child{
            font-size: 0.9em;
        }
        table tr td:last-child{
            font-size: 1.1em;
            color: #2B2B2B;
        }
    </style>
</head>
<body>
    <div class="exception">
        <?php if (1 == $code): ?>
            <h1>
                Fatal error
            </h1>
        <?php else: ?>
            <h1>
                Exception detected <?php if (!empty($code)): ?>[<?= $code ?>]<?php endif ?>
            </h1>
        <?php endif ?>
        <table>
            <tr>
                <td>
                    Message:
                </td>
                <td>
                    <?= $message ?>
                </td>
            </tr>
            <tr>
                <td>File:</td>
                <td><?= $file ?></td>
            </tr>
            <tr>
                <td>Line:</td>
                <td><?= $line ?></td>
            </tr>
        </table>
        <br /><br /><br />
        <?php foreach ($trace as $k => $item): ?>
            <h2>Trace [<?= ($k + 1) ?>]</h2>
            <table>
                <tr>
                    <td>
                        File:
                    </td>
                    <td>
                        <?= $item['file'] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Line:
                    </td>
                    <td>
                        <?= $item['line'] ?>
                    </td>
                </tr>

                <?php if (isset($item['function'])): ?>
                <tr>
                    <td>
                        Function:
                    </td>
                    <td>
                        <?= $item['function'] ?>
                    </td>
                </tr>
                <?php endif ?>

                <?php if (isset($item['class'])): ?>
                <tr>
                    <td>
                        Class:
                    </td>
                    <td>
                        <?= $item['class'] ?>
                    </td>
                </tr>
                <?php endif ?>

                <?php if (isset($item['type'])): ?>
                <tr>
                    <td>
                        Type:
                    </td>
                    <td>
                        <?= $item['type'] ?>
                    </td>
                </tr>
                <?php endif ?>

                <?php if (isset($item['args'])): ?>
                <tr>
                    <td>
                        Args:
                    </td>
                    <td>
                        <pre><?php print_r($item['args']) ?></pre>
                    </td>
                </tr>
                <?php endif ?>
            </table>
            <br /><br />
        <?php endforeach ?>
    </div>
</body>
</html>
