{literal}
<script type="text/javascript">
  	function callback (post_id, exception) {  }
  	function publish(img,product,id_product){
			var attachment = {'media':[{'type':'image','src':img,'href':img}],
							'description':'I love this product "' + product +'". Available on the shop {/literal}{$config.name}{literal}'
							};
			var actionLinks = [{ "text": "See the product", "href": "{/literal}{$shop_url}{literal}product.php?id_product="+id_product}];
			var user_message_prompt = product;
			try{
				Facebook.streamPublish('', attachment,actionLinks,'',user_message_prompt,callback,'true');
			}
			catch(erreur){

			}
		}
</script>
{/literal}
<div id="product" class="content">
	
	<p class="chemin">
		<a href="./">Home</a> >>
		{section name=id loop=$categories}
				{if $categories[id].id==$product.id_category}
					<a href="category.php?{$categories[id].lien}">{$categories[id].nom}</a>
				{/if}
		{/section}
		>> <span class="current_page">{$product.name}</span>
	</p>
	{if !isset($product)}
		<p class="noProd">This product can not be loaded. Please try again.</p>
	{elseif $product.name==''}
		<p class="noProd">This product does not exist.</p>
	{else} 
		<h1>{$product.name}</h1>
		<div id="product_img">
				<img id="cover" src="{$product.image_cover}" />
		</div>
		<div id="product_infos">
			<div id="product_description_short">
				{$product.description_short}
				<p class="button">
					<a class="button_info buttons" href="{$shop_url}{$product.link}">More details</a>
					<p class="publish">
						<a class="button_info buttons" onclick="publish('{$product.image_cover}','{$product.name}','{$product.id}')">Share !</a>
					</p>
				</p>
			</div>
			<div id="product_price">
				{if $product.reduction == 1}
					<p class="price"> Prix Réduit !</p>
					<p id="price" class="price">{$product.price_reduct}</p>
                                {else}
                                        <p id="price" class="price">{$product.price}</p>
				{/if}
				<form action="{$shop_url}checkout/cart/add" method="get">
					<label for="qty">Quantity : </label>
					<input type="text" size="2" value="1" name="qty" id="qty"/><br />
					{$product.quantity} products in stock </p>
					<input type="hidden" value="{$product.id}" name="product" />
					{if $declinaisons}
					<p class="declin">
					<label>Attributes :</label> 
					<select name="idCombination">
					{foreach from=$declinaisons item=itemDeclin}
						<option value="{$itemDeclin.id}">{$itemDeclin.name}</option>
					{/foreach}
					</select>
					</p>
					{/if}
					<p>
						<input type="submit" value="Add to cart" class="add_to_cart" src="{$customer_url}template/img/button-medium_exclusive.gif" />
					</p>
				</form>
			</div>
		</div>
		<div id="product_all_images">
			{if $images|@count > 1}
					{foreach from=$images item=image key=id}
						<a href="{$image}"><img class="all_images" src="{$image}" /></a>
					{/foreach}
				{/if} 
		</div>
		{if $product.description!=''}
		<div id="product_description">
			<h2>Description</h2>
			<p>{$product.description}</p>
		</div>
		{/if}
	{/if}
</div>
{if $tags|@count > 0}
<div id="Tags" class="box_left">
	<h1>Tags</h1>
	<div id="tags">
		<li>
			{section name=id loop=$tags}
				<a href="{$shop_url}search.php?tag={$tags[id]}">{$tags[id]}</a>
			{/section}
		</li>
	</div>
</div>
{/if}