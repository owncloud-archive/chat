<!-- <div id="chat" class="section"> -->
<!--     <h2>Chat</h2> -->
<!--     <p> -->
<!--         <div id="chat-backends"> -->
<!--             <h3>Backends</h3> -->
            <?php foreach ($_['backends'] as $backend): ?>
<!--                 <input  -->
                    <?php echo $backend->getChecked()?>
<!--                     type="checkbox"  -->
                    data-backend="<?php echo $backend->getName() ?>" 
                    data-id="<?php echo $backend->getId() ?>"
<!--                     class="backend"  -->
                    id="<?php echo $backend->getName() ?>">
                <label for="<?php echo $backend->getName()?>">
                    <?php echo $backend->getDisplayname() ?> <br>
<!--                 </label> -->
            <?php endforeach;?>
<!--         </div> -->
<!--     </p> -->
<!-- </div> -->
