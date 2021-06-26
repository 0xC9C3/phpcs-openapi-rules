<?php

class Errors
{

	/**
	 * @OA\Schema(
	 *    schema="PaginatedShopUsers",
	 *    allOf={
	 *       @OA\Schema(
	 *          ref="#/components/schemas/PaginatedResponse",
	 *       ),
	 *       @OA\Schema(
	 *          @OA\Property(
	 *             property="results",
	 *             type="array",
	 *             @OA\Items(
	 *                ref="#/components/schemas/ShopUser",
	 *             ),
	 *          ),
	 *       ),
	 *    },
	 * )
	 * @OA\Get(
	 *    summary="get all Shop users",
	 *    path="/backend/api/v1/users/Shop_users/get-all/",
	 *    deprecated=false,
	 *    tags={
	 *       "ShopUsers",
	 *    },
	 *    security={
	 *       {
	 *          "BackendJWT":{
	 *          },
	 *       },
	 *    },
	 *    operationId="getAllShopUsers",
	 *    @OA\Parameter(
	 *       @OA\Schema(
	 *          type="number",
	 *       ),
	 *       in="query",
	 *       name="page",
	 *       description="page nr of requested page",
	 *       required=false,
	 *       example=1,
	 *    ),
	 *    @OA\Parameter(
	 *       @OA\Schema(
	 *          type="number",
	 *       ),
	 *       in="query",
	 *       name="limit",
	 *       description="limit for items page",
	 *       required=false,
	 *       example=1,
	 *    ),
	 *    @OA\Parameter(
	 *       @OA\Schema(
	 *          type="string",
	 *       ),
	 *       in="query",
	 *       name="search",
	 *       description="search for items",
	 *       required=false,
	 *       example="foo",
	 *    ),
	 *    @OA\Response(
	 *       response="200",
	 *       description="successfull response",
	 *       @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *             ref="#/components/schemas/PaginatedShopUsers",
	 *          ),
	 *       ),
	 *    ),
	 * )
	 * @param mixed $a
	 * @param mixed $b
	 * @return bool
	 */
	public function schemaAndOperation(bool $a, bool $b): bool
	{
		return true;
	}

	/**
	 * @OA\Get(
	 *    summary="get",
	 *    path="/backend/api/v1/users/Shop_users/get/{ShopUsersId}",
	 *    deprecated=false,
	 *    tags={
	 *       "ShopUsers",
	 *    },
	 *    security={
	 *       {
	 *          "BackendJWT":{
	 *          },
	 *       },
	 *    },
	 *    operationId="getShopUsers",
	 *    @OA\Parameter(
	 *       @OA\Schema(
	 *          type="number",
	 *       ),
	 *       in="path",
	 *       name="ShopUsersId",
	 *       required=true,
	 *       example=1,
	 *    ),
	 *    @OA\Response(
	 *       response="200",
	 *       description="successfull response",
	 *       @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *             ref="#/components/schemas/ShopUser",
	 *          ),
	 *       ),
	 *    ),
	 * )
	 */
	public function operationOnly(): bool
	{
		return false;
	}
}
