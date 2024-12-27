<?php foreach ($links as $link): ?>
    <li class="my-1 <?= $link['status']; ?>" data-order="<?= $link['nav_order']; ?>">
        <div class="navrow py-1 px-2 d-flex justify-content-between">
            <div>
                <?php echo $link['caption']; ?>
                <small><a href="<?php echo ($link['url_type'] == 'uri') ? site_url($link['url']) : $link['url_type'].$link['url']; ?>" target="_blank"><?php echo $link['url']; ?></a></small>
                <?= $link['status']=='draft' ? ' <small>(draft)</small>' : ''; ?>
            </div>
            <div class="text-end">
                <div class="option">
                    <a href="<?= site_url("admin/navigation/swap_link_order/{$link['id']}/{$link['area_id']}/up"); ?>" class="btn btn-sm btn-primary sort-scroll-button-up" data-area="<?= $area; ?>"><i class="fa fa-arrow-up" title="move up"></i></a>
                    <a href="<?= site_url("admin/navigation/swap_link_order/{$link['id']}/{$link['area_id']}/down"); ?>" class="btn btn-sm btn-primary sort-scroll-button-down" data-area="<?= $area; ?>"><i class="fa fa-arrow-down" title="move down"></i></a>
                    <a href="<?= site_url('admin/navigation/edit_link/'.$link['id'].'/'.$link['area_id']); ?>" class="edit btn btn-sm btn-success-outline" data-mode="edit" title="edit link"><span class="fa fa-edit"></span></a>
                    <a class="btn btn-sm btn-danger-outline remove" href="<?php echo site_url('admin/navigation/delete_link/'.$link['id'].'/'.$area); ?>" title="delete menu" onclick="return confirm('Yakin akan menghapus link ini?')"><span class="fa fa-times"></span></a>
                </div>
            </div>
        </div>

        <?php if(isset($link['children'])): ?>
            <ul class="list-unstyled navigation_list">
                <?php echo $this->load->view('admin/navigation_list', ['area' => $area, 'links' => $link['children']], true); ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
