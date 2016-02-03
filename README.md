# LWModel
Lightweight PHP model

# Usage
```
<?php
class Coolest_Model_Ever extends LWModel {}

$my_model = new Coolest_Model_Ever();
$my_model->setName('Bob')->setAge(19);

echo $my_model->getAge(); // 19
echo $my_model->getName(); // Bob

$my_model->unsetAge()->unsetName(); // Unsets both fields
?>
 ```
