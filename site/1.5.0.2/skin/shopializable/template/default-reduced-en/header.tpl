{if $config.logo!=''}
		<div id="logo">
			<a href="./"><img src="{$config.logo}" /></a>
		</div>
{/if}
<div id="categories" class="box_left">
  <div id="categories_name">
	<ul>
				{php}
					global $categoriesImbrique, $category_id;
				 	
				 	foreach ($categoriesImbrique['children'] as $children){
				 		echo afficherLi($children);
				 	}
				 	
				 	function afficherLi ($child){
				 		global $category_id;
					 	$html='
					 	<li '.($category_id==$child['id']?'class="active"':'').'>
							<a href="./category.php?'.$child['lien'].'" title="'.htmlentities($child['nom']).'">'.$child['nom'].'</a>';
							if(isset($child['children'])){
								$html.='<ul>';
							 	foreach ($child['children'] as $children){
							 		$html.=afficherLi($children);
							 	}						
								$html.='</ul>';
							}
						return $html.='
						</li>
						';
				 	}
				 	
				{/php}
	  </ul>
  </div>
</div>