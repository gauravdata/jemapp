{if $config.image!=''}
		<div id="homeImage">
			<img src="{$config.image}" />
		</div>
{/if}
{if $config.description!=''}
		<div id="boutiqueDesc">
			<p>{$config.description}</p>
		</div>
{/if}
{if $products|@count != 0}
<div id="produits_phares" class="content">
	<h1>{section name=id loop=$products}
		</h1><ul><li class="products {if ($smarty.section.id.index+1)%2==0}last_products{/if}">
			<h2> <a href="product.php?id_product={$products[id].id_product}">{$products[id].title|truncate:30}</a></h2>
			<div class="desc_li">
				<p>
					<a class="description" href="product.php?id_product={$products[id].id_product}">{$products[id].description|strip_tags|truncate:100}</a>
				</p>
			</div>
			<p>
				<a class="description" href="product.php?id_product={$products[id].id_product}"><img src="{$products[id].cover}" alt="{$products[id].title|truncate:30}" /></a>
			</p>
			<p class="price">
				{$products[id].price_bis}
			</p>
			<p class="button">
				<a class="button_info buttons" href="product.php?id_product={$products[id].id_product}">Meer informatie</a>
			</p>
			<p class="button">
				<a class="button_cart buttons" href="{$shop_url}checkout/cart/add?product={$products[id].id_product}&qty=1">Klik hier en bestel</a>
			</p>
		</li>	
	{/section}
	</ul>
</div>
{/if}