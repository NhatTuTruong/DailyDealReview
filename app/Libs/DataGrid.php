<?php

namespace App\Libs;

class DataGrid
{
    protected array $columns = [];
    protected int $totalCols = 0;
    protected string $prefix_name = 'update';

    protected string $table_attrs = "class='table table-hover table-grid-admin'";
    protected string $header_class = "grid_header";
    protected string $header_class1 = "grid_header1";
    protected string $grid_row_class = "grid_row";
    protected string $grid_row_class1 = "grid_row1";
    protected string $grid_row_class2 = "grid_row2";
    protected string $grid_row_class3 = "grid_row3";

    protected int $showEditLink = 1;
    protected string $link_target = '';
    protected bool $has_check_box = true;
    protected string $primary_key = 'id';
    protected string $link_edit = '';

    public function setPrimaryKey($primary_key): void
    {
        $this->primary_key = $primary_key;
    }

    public function setLinkEdit($link_edit): void
    {
        $this->link_edit = $link_edit;
    }

    public function addColumnLabel($col_name, $col_title = '', $attrs = "align='left'", $decode = 1, $format = ''): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name,
            'col_title' => $col_title,
            'col_type' => 'label',
            'attrs' => $attrs,
            'decode' => $decode,
            'format' => $format
        ];
        $this->totalCols++;
    }

    public function addColumnRawHtml($col_name, $col_title = '', $html = '', $attrs = "align='left'"): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name,
            'col_title' => $col_title,
            'col_type' => 'raw_html',
            'attrs' => $attrs,
            'html' => $html,
        ];
        $this->totalCols++;
    }

    public function addColumnButton($col_name = '', $col_title = '', $option = [], $attrs = "align='left'"): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name ?: 'id',
            'col_title' => $col_title,
            'col_type' => 'button',
            'attrs' => $attrs,
            'option' => $option,
        ];
        $this->totalCols++;
    }

    public function addColumnText($col_name, $col_title = '', $attrs = "align='left'"): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name,
            'col_title' => $col_title,
            'col_type' => 'text',
            'attrs' => $attrs
        ];
        $this->totalCols++;
    }

    public function addColumnImage($col_name, $col_title = '', $img_attr = '', $attrs = "align='left'"): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name,
            'col_title' => $col_title,
            'col_type' => 'image',
            'img_attr' => $img_attr ?: 'width=60px height=60px border:0',
            'attrs' => $attrs
        ];
        $this->totalCols++;
    }

    public function addColumnSelect(
        $col_name,
        $col_title = '',
        $attrs = "align='left'",
        $arrOptions = '',
        $valueSameOption = 0,
        $is_text = 0,
        $href = ''
    ): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name,
            'col_title' => $col_title,
            'col_type' => 'select',
            'attrs' => $attrs,
            'arrOptions' => $arrOptions,
            'valueSameOption' => $valueSameOption,
            'is_text' => $is_text,
            'href' => $href
        ];
        $this->totalCols++;
    }

    public function addColumnDate($col_name, $col_title = '', $attrs = "align='left'", $date_format = 'm-d-Y H:i'): void
    {
        if ($col_title == '') {
            $col_title = $col_name;
        }
        $this->columns[] = [
            'col_name' => $col_name,
            'col_title' => $col_title,
            'col_type' => 'date',
            'attrs' => $attrs,
            'date_format' => $date_format
        ];
        $this->totalCols++;
    }

    public function showColumnLabel($data_column, $value, $decode = 0)
    {
        $r_value = (is_numeric($value) && $value < 0) ? "<span class='red'>$value</span>" : $value;
        if ($data_column['format'] == 'number') {
            $r_value = number_format($value);
            return (is_numeric($value) && $value < 0) ? "<span class='red'>$r_value</span>" : $r_value;
        }
        if ($data_column['format'] == 'percent') {
            $r_value = number_format($value);
            return (is_numeric($value) && $value < 0) ? "<span class='red'>" . $r_value . "%</span>" : $r_value . '%';
        }

        if (strlen($value) > 50) {
            $r_value = substr($value, 0, 50) . '...';
        }
        if ($decode == 1) {
            return $r_value;
        }
        if ($r_value == '') {
            return '—';
        }
        return $r_value;
    }

    public function showColumnImage($data_column, $value)
    {
        $ext_allow = ' .jpg, .jpeg, .gif, .png, .svg';
        $ext = strtolower(strrchr($value, '.'));
        if (!empty($value)) {
            $html = "<img src='" . "$value' " . $data_column['img_attr'] . " title='" . $value . "' style='background:#eaeaea' onerror=\"this.onerror=null;this.src='/images/store.webp';\">";
        } else {
            $html = "<img src='/images/store.webp' " . $data_column['img_attr'] . " title='No Image' style='background:#eaeaea'>";
        }
        return $html;
    }

    public function showColumnText($data_column, $value, $id)
    {
        $name = $this->prefix_name . "[$id][" . $data_column['col_name'] . "]";
        return "<input type='text' name=\"" . $name . "\" id='" . $data_column['col_name'] . "' value=\"$value\" >";
    }

    public function showColumnRawHtml($data_column, $value)
    {
        return $data_column['html'];
    }

    public function showColumnButton($data_column, $value)
    {
        $view = data_get($data_column, 'option.view', '');
        $edit = data_get($data_column, 'option.edit', '');
        $clone = data_get($data_column, 'option.clone', '');
        $delete = data_get($data_column, 'option.delete', '');
        $html = '';

        if ($view) {
            $route = data_get($view, 'route', '');
            if ($route == 'post_detail') {
                $link = route($route, ['slug' => $value->slug, 'id' => $value->id]);
            } else {
                $link = route($route, $value->slug);
            }
            $html .= "<a class='btn btn-success btn-sm mr-1' href=\"$link\" title='Xem chi tiết' target='_blank'>
                <i class='fas fa-eye'></i>
            </a>";
        }

        if ($edit) {
            $route = data_get($edit, 'route', '');
            $link = route($route, $value->id);
            $html .= "<a class='btn btn-info btn-sm mr-1' href=\"$link\" title='Chỉnh sửa'>
                <i class='fas fa-pencil-alt'></i>
            </a>";
        }

        if ($clone) {
            $route = data_get($clone, 'route', '');
            $link = route($route, $value->id);
            $html .= "<a class='btn btn-primary btn-sm mr-1' href=\"$link\" title='Sao chép'>
                <i class='fas fa-copy'></i>
            </a>";
        }

        if ($delete) {
            $route = data_get($delete, 'route', '');
            $link = route($route, $value->id);
            $html .= "<a class='btn btn-danger btn-sm' href=\"$link\" title='Xóa'>
                <i class='fas fa-trash'></i>
            </a>";
        }


        return $html;
    }

    public function showColumnSelect($data_column, $value, $id = "")
    {
        $name = $this->prefix_name . "[$id][" . $data_column['col_name'] . "]";
        $valueSameOption = $data_column["valueSameOption"];
        $html = "<select name='" . $name . "'>";
        if (is_array($data_column["arrOptions"])) {
            foreach ($data_column["arrOptions"] as $key => $val) {
                $val1 = ($valueSameOption == 1) ? $val : $key;
                $selected = ($val1 == $value) ? "selected" : "";
                if ($data_column["is_text"] == 1 && $val1 == $value) {
                    if ($data_column["href"] == "") {
                        return $val;
                    } else {
                        $href = str_replace("%1%", $val1, $data_column["href"]);
                        return "<a href='$href'>$val</a>";
                    }
                }
                $html .= "<option value='$val1' $selected >$val</option>\n";
            }
        }
        $html .= "</select>";
        if ($data_column["is_text"] == 1) {
            return "";
        }
        return $html;
    }


    public function showColumnDate($data_column, $value, $id): string
    {
        if ($value == '' || $value == '0') {
            return 'N/A';
        }
        return date_format($value, $data_column['date_format']);
    }


    public function showColumn($data_column, $value, $id = '', $row = ''): string
    {
        $html = '';
        if (data_get($data_column, 'func')) {
            $html .= $data_column['func']($data_column, $value, $id, $row);
        } else {
            switch ($data_column['col_type']) {
                case 'label'    :
                    $html .= $this->showColumnLabel($data_column, $value);
                    break;
                case 'text'        :
                    $html .= $this->showColumnText($data_column, $value, $id);
                    break;
                case 'select'    :
                    $html .= $this->showColumnSelect($data_column, $value, $id);
                    break;
                case 'date'        :
                    $html .= $this->showColumnDate($data_column, $value, $id);
                    break;
                case 'url'        :
                    $html .= $this->showColumnUrl($data_column, $value, $id);
                    break;
                case 'image'    :
                    $html .= $this->showColumnImage($data_column, $value, $id);
                    break;
                case 'raw_html'    :
                    $html .= $this->showColumnRawHtml($data_column, $value);
                    break;
                case 'button'    :
                    $html .= $this->showColumnButton($data_column, $row);
                    break;
            }
        }
        return $html;
    }

    public function showDataGrid($data, $paginate = 100, $total_row = 100): string
    {
        $page = $_GET['page'] ?? 1;
        $html = '<table ' . $this->table_attrs . '>';
        $html .= $this->renderTableHeader();

        if ($data) {
            $has_row = 0;
            foreach ($data as $key => $val) {
                $id = data_get($val, $this->primary_key, 'id');
                $has_row++;
                $row_class = ($key < $paginate - 1 && $key < $total_row - 1) ? $this->grid_row_class : $this->grid_row_class2;
                $html .= '<tr id="grid_tr_' . $key . '">';
                $html .= "<td width='1%' align='center' class='$row_class pl-0' style='color:#666666'>" . ($key + 1 + $paginate * ($page - 1)) . "</td>\n";
                if ($this->has_check_box) {
                    $html .= '<td width="1%" class="' . $row_class . '">
                    <div class="icheck-primary">
                        <input type="checkbox" class="checker chk" id="grid_chk_' . $key . '" value=' . $id . '>
                        <label for="grid_chk_' . $key . '"></label>
                    </div>
                    </td>';
                }
                foreach ($this->columns as $k => $v) {
                    $col_name = data_get($v, 'col_name');
                    if ($v['col_type'] != "hidden") {
                        if ($k < $this->totalCols - 1) {
                            $row_class = ($key < $paginate - 1 && $key < $total_row - 1) ? $this->grid_row_class : $this->grid_row_class2;
                        } else {
                            $row_class = ($key < $paginate - 1 && $key < $total_row - 1) ? $this->grid_row_class1 : $this->grid_row_class3;
                        }
                        if ($k == 0 && $this->showEditLink == 1) {
                            $href = route($this->link_edit, $id);
                            $html .= '<td ' . $v['attrs'] . ' class="' . $row_class . '"><a href="' . $href . '" target="' . $this->link_target . '">' .
                                $this->showColumn($v, $val->$col_name, '', $val) . '</a></td>';

                        } else {
                            $column_value = $this->showColumn($v, $val->$col_name, $val->id, $val);

                            if ($column_value == "") {
                                $column_value = " & nbsp;";
                            }


                            $html .= '<td ' . $v['attrs'] . ' class="' . $row_class . '">' . $column_value . '</td>' . "\n";
                        }
                    }
                }
                $html .= '</tr>' . "\n";
            }
            if ($has_row == 0) {
                $html .= '<tr><td colspan=' . ($this->totalCols + 2) . " > " . "<span class = 'text-danger' > No data !</span > " . '</td></tr>' . "\n";
            }
        }
        $html .= '</table>' . "\n";
        return $html;
    }

    public function renderTableHeader()
    {
        $html = '';
        $html .= '<thead><tr>';
        if (is_array($this->columns)) {
            $html .= '<th width="1% " class="' . $this->header_class . '"></th>';
            $html .=
                "<th width = '1%' class=\"$this->header_class\">" .
                "<div class='icheck-primary'>" .
                "<input type='checkbox' class='checkall' id='id_checkbox'>" .
                "<label for='id_checkbox'></label>" .
                "</div>" .
                "</th>";

            foreach ($this->columns as $key => $column) {
                if ($column['col_type'] != "hidden") {
                    $head_class = ($key < $this->totalCols - 1) ? $this->header_class : $this->header_class1;
                    $html .= '<th ' . $column['attrs'] . ' class="' . $head_class . '" >';

                    $html .= $column['col_title'];
                    $html .= "</th>";
                }
            }
        }
        $html .= '</tr></thead>';
        return $html;
    }

    public function showColumnUrl($c, $value): array|string
    {
        return str_replace("%1%", $value, $c["tagA"]);
    }
}
