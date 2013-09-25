<div id="category" class="content">
	<p class="chemin">
		<a href="./">Home</a> >> <span class="current_page">{$category.name}</span>
	</p>
	{if !isset($category)}
		<p class="noProd">This category cannot be loaded.</p>
	{elseif $category.name==''}
		<p class="noProd">This category does not exist.</p>
	{else}
  <div style="clear:both"></div>
		{if $category.productNumber==0}
			<p class="noProd">No products here.</p>
		{else}
		<ul>
			{section name=id loop=$products}
				<li class="products_list{if ($smarty.section.id.index+1)%2==0} last_products{/if}">
					<h2> <a href="product.php?id_product={$products[id].id_product}">{$products[id].title|truncate:30}</a></h2>
					<div class="desc_li">
					<p>
						<a class="description" href="product.php?id_product={$products[id].id_product}">{$products[id].description|strip_tags|truncate:100}</a>
					</p>
					</div>
					<p>
						<a class="description" href="product.php?id_product={$products[id].id_product}"><img src="{$products[id].cover}" /></a>
					</p>
					<p class="price">
						{$products[id].price_bis}
					</p>
					<p>
					{if $products[id].quantity ne 0}
						<p class="stock_1">In stock</p>
					{else}
						<p class="stock_0">Out of stock</p>
					{/if}
					</p>
					
					<p class="button">
						<a class="button_info buttons" href="product.php?id_product={$products[id].id_product}">More info</a>
					</p>
					<p class="button">
						<a class="button_cart buttons" href="{$shop_url}checkout/cart/add?product={$products[id].id_product}&qty=1">Add to cart</a>
					</p>
				</li>
			{/section}
		</ul>
		{/if}
	{/if}
	<p class="pagination">
		{if $category.productNumber>$config.number_product}
                    <span>
                        {if $page>0}
                                <a href="category.php?categorie={$category_id}&page={$page-1}"><</a>
                        {/if}

                        {section name=pagination start=1 loop=$category.productNumber/$config.number_product+2 step=1}
                            {if ($page+1)==$smarty.section.pagination.index}
                                <span class="current_page">{$page+1}</span>
                            {else}
                                <span class="page"><a href="category.php?categorie={$category_id}&page={$smarty.section.pagination.index-1}">{$smarty.section.pagination.index}</a></span>
                            {/if}
                        {/section}
                        {if ($page*$config.number_product)<$category.productNumber}
                                <a href="category.php?categorie={$category_id}&page={$page+1}">></a>
                        {/if}
                    </span>
		{/if}
	</p>
</div>